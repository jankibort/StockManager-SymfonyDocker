const products = document.getElementsByClassName('data-delete');

for (let product of products) {
    product.addEventListener('click', () => {
        if (confirm("Are you sure?")) {
            const id = product.getAttribute('data-id');

            fetch(`/products/delete/${id}`, {
                method: 'DELETE'
            }).then(res => window.location.reload()).catch(alert("some kind of error ..."));
        }
    })
}
