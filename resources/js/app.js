window.autoResizeTextArea = () => ({
    resize() {
        this.$el.style.height = this.$el.scrollHeight + "px";
    },
    init() {
        this.resize();
        this.$el.addEventListener("input", () => {
            this.resize();
        });
    },
});
