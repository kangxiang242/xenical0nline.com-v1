const FormHelper = {
    submitting: false,

    validators: {
        required: v => (!v || v.toString().trim() === "") ? "不能為空" : "",
        email: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? "" : "郵箱格式不正確",
        phone: v => /^09\d{8}$/.test(v) ? "" : "手機號格式不正確",
        number: v => (!isNaN(v) && v !== null && v !== "") ? "" : "必須是數字",
        url: v => /^(https?:\/\/)?([\w.-]+)\.[a-z]{2,6}.*$/.test(v) ? "" : "URL 格式不正確",
        password: v => (v && v.length >= 6) ? "" : "密碼至少6位",
        minLength: (v, len) => (v ?? "").toString().length < len ? `長度不能少於 ${len}` : "",
        maxLength: (v, len) => (v ?? "").toString().length > len ? `長度不能超過 ${len}` : "",
        regex: (v, reg) => !reg.test(v ?? "") ? "格式不正確" : "",
    },

    submit(formSelector, config = {}) {
        const form = typeof formSelector === "string" ? document.querySelector(formSelector) : formSelector;
        if (!form) return console.error("Form not found");

        if (this.submitting) return;
        this.submitting = true;

        const submitBtn = config.btnSelector
            ? document.querySelector(config.btnSelector)
            : form.querySelector("button[type=submit]");

        if (submitBtn) {
            this.activeButton(submitBtn);
        }


        this.clearErrors(form);

        const formData = new FormData(form);

        this.validate(form, formData, config).then(errors => {
            if (errors.length > 0) {
                this.showErrors(form, errors);
                config.onValidateFail?.(errors);
                this.restoreButton(submitBtn);
                this.submitting = false;
                return;
            }

            this.doAjax(form, formData, config, submitBtn);
        });
    },

    doAjax(form, formData, config, submitBtn) {
        config.beforeSend?.(formData);

        const method = (config.method || form.method || "POST").toUpperCase();
        const url = config.url || form.action;

        const fetchOptions = { method };

        if (config.json === true) {
            fetchOptions.headers = { "Content-Type": "application/json" };
            fetchOptions.body = JSON.stringify(Object.fromEntries(formData.entries()));
        } else {
            if (method === "GET") {
                const params = new URLSearchParams(formData).toString();
                fetch(url + (url.includes("?") ? "&" : "?") + params, fetchOptions);
            } else {
                fetchOptions.body = formData;
            }
        }

        const successHandler = config.onSuccess || function(data) {

            if(data.redirect){
                window.location.href = data.redirect;
            }else{
                if(data.status == 'success'){
                    Swal.fire({
                        icon: "success",
                        text: data.message || "操作成功",
                        timer: 1500,
                        showConfirmButton: false,
                    });
                }else{
                    Swal.fire({
                        icon: "error",
                        text: data.message || "操作失败",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            }
        };
        const errorHandler = config.onError || function(err) {

            Swal.fire({
                icon: 'error',
                text: err.message || "服務器錯誤",
                timer: 1500,
                showConfirmButton: false,
            });
        };
        fetch(url, fetchOptions)
            .then(r => r.json())
            .then(successHandler)
            .catch(errorHandler)
            .finally(() => {
                this.restoreButton(submitBtn);
                this.submitting = false;
            });
    },

    async validate(form, formData, config = {}) {
        const rules = config.rules || {};
        const errors = [];

        for (const field in rules) {
            const rawRule = rules[field];
            let value = formData.get(field);

            let ruleObj = {};
            if (typeof rawRule === "string") {
                ruleObj = this.parseRuleString(rawRule);
            } else {
                ruleObj = Object.assign({}, rawRule);
                if (typeof ruleObj.type === "string") {
                    const parsed = this.parseRuleString(ruleObj.type);
                    ruleObj = Object.assign(parsed, ruleObj);
                }
            }

            const getMessage = (ruleName, defaultMsg) => {
                if (ruleObj.messages?.[ruleName]) return ruleObj.messages[ruleName];
                if (config.messages?.[field]?.[ruleName]) return config.messages[field][ruleName];
                if (config.messages?.[ruleName]) return config.messages[ruleName];
                return defaultMsg || "验证不通过";
            };

            // ---- 统一 validator 执行 ----
            const validatorList = [];
            if (ruleObj.required) validatorList.push({ name: "required" });
            if (ruleObj.minLength) validatorList.push({ name: "minLength", param: ruleObj.minLength });
            if (ruleObj.maxLength) validatorList.push({ name: "maxLength", param: ruleObj.maxLength });
            if (ruleObj.regex) validatorList.push({ name: "regex", param: ruleObj.regex });
            if (ruleObj.type) {
                ruleObj.type.split("|").forEach(t => validatorList.push({ name: t }));
            }

            for (const vItem of validatorList) {
                const vName = vItem.name;
                const param = vItem.param;

                if (!this.validators[vName]) continue;
                if(vName == 'phone'){
                    value = value.replace(/\s+/g, '');
                }

                let msg = this.validators[vName](value, param);
                if (msg) msg = getMessage(vName, msg);

                if (msg) {
                    errors.push({ field, message: msg });
                    if (vName === "required") break; // required 失败跳过后续
                }
            }

            // ---- custom sync ----
            if (typeof ruleObj.custom === "function") {
                const msg = ruleObj.custom(value, formData);
                if (msg) errors.push({ field, message: getMessage("custom", msg) });
            }

            // ---- custom async ----
            if (typeof ruleObj.async === "function") {
                const msg = await ruleObj.async(value, formData);
                if (msg) errors.push({ field, message: getMessage("async", msg) });
            }
        }

        return errors;
    },

    parseRuleString(ruleStr) {
        const parts = ruleStr.split("|").map(p => p.trim());
        const obj = {};
        const types = [];

        for (const p of parts) {
            if (p === "required") obj.required = true;
            else if (p.startsWith("min:")) obj.minLength = parseInt(p.split(":")[1]);
            else if (p.startsWith("max:")) obj.maxLength = parseInt(p.split(":")[1]);
            else if (p.startsWith("regex:")) {
                const regStr = p.replace("regex:", "");
                const m = regStr.match(/^\/(.+)\/([a-z]*)$/i);
                obj.regex = m ? new RegExp(m[1], m[2]) : new RegExp(regStr);
            } else types.push(p);
        }

        if (types.length) obj.type = types.join("|");
        return obj;
    },

    showErrors(form, errors) {
        if (!errors || errors.length === 0) return;
        const firstError = errors[0];
        Swal.fire({
            icon: 'error',
            iconColor: '#fff',
            text: firstError.message,
            color: '#fff',
            background: 'rgba(0,0,0,0.7)',
            width: 'auto',
            backdrop: false,
            timer: 1000,
            timerProgressBar: false,
            showConfirmButton: false,
        });
    },

    clearErrors(form) {
        form.querySelectorAll(".input-error").forEach(e => e.classList.remove("input-error"));
        form.querySelectorAll(".form-error").forEach(e => e.remove());
    },
    activeButton(btn) {
        if (!btn) return;
        btn.disabled = true;
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto block" style="width:50px;height:11px;" viewBox="0 0 120 30" fill="currentColor"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle></svg>
        `;
    },
    restoreButton(btn) {
        if (!btn) return;
        btn.disabled = false;
        if (btn.dataset.originalText) btn.innerHTML = btn.dataset.originalText;
    }
};
