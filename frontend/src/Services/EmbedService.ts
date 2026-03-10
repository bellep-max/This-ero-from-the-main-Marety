export const setEmbedCode = (size: number | string, url: string): string => {
    return `<iframe width="100%" height="${size}" frameborder="0" src="${url}"></iframe>`;
};

export default { setEmbedCode };
