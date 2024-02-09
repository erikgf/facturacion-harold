const ButtonLoading = function({$}){
    const STR_LOADING  = Util.STR_CARGANDO;
    let tempHTML = "";

    const init = () => {
        this.isLoading = false;
        this.isDestroyed = false;
        if (!$){
            return null;
        }
        this.$ = $;
        tempHTML = $.innerHTML;
    };

    this.start = () => {
        this.loading(true);
    }

    this.finish = () => {
        this.loading(false);
        this.destroy();
    };

    this.loading = (isLoading) => {
        if (this.isDestroyed) return;
        this.isLoading = isLoading;

        if (isLoading){
            this.$.disabled = true;
            this.$.innerHTML = STR_LOADING;
        } else {
            this.$.disabled = false;
            this.$.innerHTML = tempHTML;
        }
    };

    this.destroy = () => {
        this.$ = null;
        this.isDestroyed = true;
    }

    return init();
};