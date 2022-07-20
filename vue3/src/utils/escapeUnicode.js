export function escapeUnicode(str) {
    return unescape(encodeURIComponent(str))
}
