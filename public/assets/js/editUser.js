
window.onload = function () {
    document.querySelectorAll('.edit-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const id = this.getAttribute('data-input-id');
            e.preventDefault();
            toggleVisibility(id);
        })
    })
}

function toggleVisibility(id) {
    const el = document.querySelector(`#edit_user_${id}`)
    document.querySelector(`#edit_user_${id}`).readOnly = !el.readOnly;
}


// Gestion de modal de suppression

$("#deleteUserModal").on('show.bs.modal', function (e) {

    // Recuperer l'identifiant de l'association a supprimer
    const userID = $(e.relatedTarget).data('user-id');
    const deleteUrl = 'user/dashboard/remove';

    // Ajout de l'identifiant a l'attribut href
    $("#deleteUserButton").attr('href', `${deleteUrl}/${userID}`);
})