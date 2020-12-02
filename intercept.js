// https://my.account.sony.com/central/signin/?duid=00000007000901002ffe132f3275f2c6c581276b3b3ea1c581a41f31a9fa4bb9194d7a8cf2604a37&response_type=code&client_id=e4a62faf-4b87-4fea-8565-caaabb3ac918&scope=web%3Acore&access_type=offline&state=335d242f0d66f8e0fc7d8f98ccec05764ed91022a5b5f110c38f68f7849657cf&service_entity=urn%3Aservice-entity%3Apsn&ui=pr&redirect_uri=https%3A%2F%2Fweb.np.playstation.com%2Fapi%2Fsession%2Fv1%2Fsession%3Fredirect_uri%3Dhttps%253A%252F%252Fstore.playstation.com%252Fen-us%252Flatest%26x-psn-app-ver%3D%2540sie-ppr-web-session%252Fsession%252Fv4.3.2&auth_ver=v3&error=login_required&error_code=4165&error_description=User+is+not+authenticated&no_captcha=true&cid=05eaf704-f788-47c1-8e6e-ecbaa1dde877#/signin/ca?entry=ca
(function(open) {
    XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {

        this.addEventListener("readystatechange", function() {
            if (this.readyState == XMLHttpRequest.DONE) {
                let response = JSON.parse(this.responseText);

                if (response && "npsso" in response) {
                    console.log('found npsso', response.npsso);
                }
            }
        }, false);

        open.call(this, method, url, async, user, pass);
    };

    window.onbeforeunload = function(){
        return 'Are you sure you want to leave?';
    };

})(XMLHttpRequest.prototype.open);