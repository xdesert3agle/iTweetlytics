function resize() {
    this.style.height = "auto";
    this.style.height = `${this.scrollHeight}px`;
}

export const setResizeListeners = ($el, query) => {
    const targets = $el.querySelectorAll(query);
    targets.forEach(target => {
        target.style.height = `${target.scrollHeight > 0 ? target.scrollHeight : 72}px`;
        target.addEventListener("input", resize);
    });
};
