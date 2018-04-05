
module.exports = (e, addHandler) => {
    if (!e.shiftKey || [38, 40].indexOf(e.keyCode) === -1) {
        return;
    }

    e.preventDefault();

    let td = e.target;
    while ((td = td.parentElement) && td.tagName.toLowerCase() !== 'td') {} // eslint-disable-line no-empty

    const tr = td.parentNode;
    const trs = Array.prototype.slice.call(tr.parentNode.childNodes);
    const columnIndex = Array.prototype.slice.call(tr.childNodes).indexOf(td);
    const rowIndex =  trs.indexOf(tr);
    const nextRowIndex = e.keyCode === 38 ? rowIndex - 1 : rowIndex + 1;

    if (nextRowIndex >= 0 && nextRowIndex < trs.length) {
        trs[nextRowIndex].childNodes[columnIndex].querySelector('input, select').focus();
    } else if (nextRowIndex === trs.length) {
        addHandler();
        setTimeout(() => {
            tr.parentNode.childNodes[nextRowIndex].childNodes[columnIndex].querySelector('input').focus();
        });
    }
};