export default class MyFormData {
    constructor(data, headers) {
        // Assign the keys with the current object MyFormData so we can access directly to the data: 
        // (new FormData({myvalue: "hello"})).myvalue; // returns 'hello'
        Object.assign(this, data);

        // Preserve the originalData to know which keys we have and/or reset the form
        this.originalData = JSON.parse(JSON.stringify(data));

        this.form = null;
        this.errors = {};
        this.submitted = false;
        this.headers = headers || {}
    }

    // https://stackoverflow.com/a/42483509/8068675
    // It will build a multi-dimensional Formdata
    buildFormData(data, parentKey) {
        if (data && typeof data === 'object' && !(data instanceof Date) && !(data instanceof File) && !(data instanceof Blob)) {
            Object.keys(data).forEach(key => {
                this.buildFormData(data[key], parentKey ? `${parentKey}[${key}]` : key);
            });
        } else {
            const value = data == null ? '' : data;
            this.form.append(parentKey, value);
        }
    }

    // Returns all the new / modified data from MyFormData
    data() {
        return Object.keys(this.originalData).reduce((data, attribute) => {
            data[attribute] = this[attribute];
            return data;
        }, {});
    }

    post(endpoint) {
        return this.submit(endpoint);
    }

    patch(endpoint) {
        return this.submit(endpoint, 'patch');
    }

    delete(endpoint) {
        return axios.delete(endpoint, {}, this.headers)
            .catch(this.onFail.bind(this))
            .then(this.onSuccess.bind(this));
    }

    submit(endpoint, requestType = 'post') {
        this.form = new FormData();
        this.form.append('_method', requestType);
        this.buildFormData(this.data());

        return axios.post(endpoint, this.form, {
            headers: {
                'Content-Type': `multipart/form-data; boundary=${this.form._boundary}`,
            }
        })
            .catch(this.onFail.bind(this))
            .then(this.onSuccess.bind(this));
    }

    onSuccess(response) {
        this.submitted = true;
        this.errors = {};

        return response;
    }

    onFail(error) {

        console.log(error);

        this.errors = error.response.data.errors;
        this.submitted = false;

        throw error;
    }

    reset() {
        Object.assign(this, this.originalData);
    }
}