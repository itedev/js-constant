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
        get: function () {
            var args = $.extend([], arguments);
            var name = '';
            var alias = '';

            if (args.length == 1) {
                name = args.pop();
            } else if (args.length == 2) {
                name = args.pop();
                alias = args.pop();
            } else {
                console.error('Invalid number of arguments');

                return null;
            }

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