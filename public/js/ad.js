$('#add-image').click(function() {
    const index = +$('#widgets-counter').val();
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);
    $('#ad_images').append(tmpl);

    $('#widgets-counter').val(index+1);
    handleDeleteButtons();
})

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function() {
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter() {
    const count = +$('#ad_images div.form-group').length;
    $('#widgets-counter').val(count);
}
updateCounter();
handleDeleteButtons();