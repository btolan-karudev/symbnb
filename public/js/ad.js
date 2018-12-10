handleDeleteButtons();
updateCounter();

$('#add-image').click(function () {
    // je vais recuperer le numer des futures champs que je vais creer
    const index = +$('#widgets-counter').val();

    // je recupere les prototype des entrees
    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g, index);

    //j injecte ce code au sein de la div
    $('#annonce_images').append(tmpl);

    //incremente le conteur pour avoir un nouveau id unique
    $('#widgets-counter').val(index + 1);

    // manipulation des button supprimer
    handleDeleteButtons();

});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter() {
    const count = +$('#annonce_images div.form-group').length;

    $('#widgets-counter').val(count);
}
