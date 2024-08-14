

const urlParams = new URL(window.location);
var data = {
    url: 'http://localhost:3000',
    mainName: 'CRM System',
    title: ''
}

switch (urlParams.pathname) {
    case '/':
        if (localStorage.getItem("token") !== null) {
            window.location = '/customers.html';
        }

        data.title = 'Welcome';

        break;

    case '/customers.html':
        if (localStorage.getItem("token") == null) {
            window.location = '/';
        }
        console.log(111);

        $.ajax({
            url: data.url + '/customers',
            method: 'GET',
            async: false, // Synchronous request
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem("token"),
            },
            success: function (response) {

            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.responseJSON.status == 'authError') {
                    localStorage.removeItem('token');
                    window.location = '/';
                }
            }
        });

        data.title = 'Customers';

        break;

    default:
        break;
}

$('.js-title, title').text([data.mainName, data.title].join(' - '));

function onSubmitLoginRegisterForm(form, event) {
    event.preventDefault(); // Prevent form from submitting the traditional way

    fetch(data.url + $(form).attr('action'), { // Replace with your API endpoint
        method: 'POST',
        body: new FormData(form)
    })
        .then(response => response.json())
        .then(data => {

            $(form).find('.responseMessage').text('');

            if (data.status == 'error') {

                $(form).find('.responseMessage').append(`<div class="alert alert-danger">${data.msg}</div>`);

                $.each(data.data, (i, v) => {
                    $(form).find('.responseMessage').append(`<div class="alert alert-danger">${v.join('<br>')}</div>`);
                })
            }

            if (data.status == 'success') {
                $(form).find('.responseMessage').append(`<div class="alert alert-success">${data.msg}</div>`);
                if (data.data.token !== undefined)
                    localStorage.setItem('token', data.data.token);
                if (data.data.redirect !== undefined)
                    window.location = data.data.redirect;
            }

        })
}

function ajaxRequest(params) {
    $.ajax({
        url: data.url + '/customers',
        method: 'GET',
        async: false, // Synchronous request
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem("token"),
        },
        success: function (res) {
            params.success(res.data)
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseJSON.authError) {
                localStorage.removeItem('token');
                window.location = '/';
            }
        }
    });
}