

function ajax_delete_fum(id)
{
    if(confirm('Wirklich löschen?'))
    {
        $.ajax(location.href, {
            type: 'POST',
            async: true,
            data: {
                delete: 'true',
                fum: id
            }
        });
    }
    else
    {

    }
}