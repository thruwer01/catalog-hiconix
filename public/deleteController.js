window.onload = (e) => {
    let url = e.target.URL;
    let pathname = new URL(url).pathname;

    const addAttachmentsDelete = () => {
        let allImg = document.querySelectorAll('svg#imgAttach');
        let allDoc = document.querySelectorAll('svg#docAttach');
        
        if (allImg.length > 0)
        {
            allImg.forEach(img => {
                img.onclick = (event) => {
                    let divData = event.target.closest('div');
                    let attachID = divData.dataset.idAttach;
                    let productID = divData.dataset.idProduct;
                    divData.remove();
                    //удаляем
                    axios.post('/attachments/images', {attachID: attachID, productID: productID})
                }
            });
        }

        if (allDoc.length > 0)
        {
            allDoc.forEach(doc => {
                doc.onclick = (event) => {
                    let divData = event.target.closest('div');
                    let attachID = divData.dataset.idAttach;
                    let productID = divData.dataset.idProduct;
                    divData.remove();
                    //удаляем
                    axios.post('/attachments/documents', {attachID: attachID, productID: productID})
                }
            })
        }
    }

    const onloadHandler = (event) => {
        let pathname = new URL(event.detail.url).pathname;
        if (pathname === '/products') {
            addAttachmentsDelete();
        }
    }

    document.addEventListener("turbo:load", onloadHandler, false);
    if (pathname === '/products') addAttachmentsDelete();
}