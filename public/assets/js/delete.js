// GEstion de modal de suppression

$("#deleteProductModal").on('show.bs.modal', function (e) {

    // Recuperer l'identifiant de l'association a supprimer
    const productID = $(e.relatedTarget).data('product-id');
    const deleteUrl = 'delete';

    // Ajout de l'identifiant a l'attribut href
    $("#deleteProductButton").attr('href', `${deleteUrl}/${productID}`);
})