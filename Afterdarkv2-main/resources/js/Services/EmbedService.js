export const setEmbedCode = (size, url) => {
    return `<iframe width="100%" height="${size}" frameborder="0" src="${url}"></iframe>`;
};

export default { setEmbedCode };
