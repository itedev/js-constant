(function () {
    var Constants = function () {
        this.constants = {};
    };

    Constants.prototype = {
        addConstant: function (constant) {
            if (null == constant.alias) {
                constant.alias = '';
            }
            if (typeof this.constants[constant.alias] == 'undefined') {
                this.constants[constant.alias] = {};
            }
            this.constants[constant.alias][constant.name] = constant.value;
        },
        getConstant: function (name, alias) {
            alias = alias || '';
            if (typeof this.constants[alias] == 'undefined') {
                console.error('Alias "' + alias + '" was not found.');

                return null;
            }
            if (typeof this.constants[alias][name] == 'undefined') {
                console.error('Constant "' + name + '" was not found.');

                return null;
            }

            return this.constants[alias][name];
        }
    };

    SF.fn.constants = new Constants();
    SF.classes.Constants = Constants;

})();