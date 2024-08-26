

const urlParams = new URL(window.location);
var data = {
    url: 'http://localhost:3000',
    mainName: 'CRM System',
    title: ''
}

switch (urlParams.pathname) {
    case '/':
        if (loggedIn())
            window.location = '/customers';

        data.title = 'Welcome';

        break;

    case '/customers':
        if (!loggedIn())
            window.location = '/';

        loadAllCustomers();



        data.title = 'Customers';

        break;

    case '/customers/edit':
        if (!loggedIn())
            window.location = '/';

        data.title = 'Customers';

        break;

    default:
        break;
}

if( loggedIn() ){
    data.title += ` <span onclick="return logOut();" style="font-size:1rem; color:red; cursor:pointer;">Logout</span>`;
}

$('.js-title, title').html([data.mainName, data.title].join(' - '));



function onSubmitForm(form, event) {
    event.preventDefault();

    fetchRequest('POST', $(form).attr('action'), 0, form).then(data => {

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

    });
}

function loadAllCustomers() {
    fetchRequest('GET', '/customers', 1).then(data => {
        $('#customers').bootstrapTable('load', data.data);
    })
}

function actionFormatter(value, row, index) {
    return `
        <button class="btn btn-primary edit js-edit" data-id="${row.ID}">Edit</button>
        <button class="btn btn-danger delete js-delete" data-id="${row.ID}">Delete</button>
    `;
}

$(document).ready(function () {
    setTimeout(function () {
        var $table = $('#customers');
        $table.on('click', ".js-delete", function (e) {
            if (confirm('Are you sure?')) {
                fetchRequest('DELETE', '/customers/delete/' + this.dataset.id, 1).then(data => {
                    if (data.status == 'error') {
                        alert(data.msg);
                    }
                    if (data.status == 'success') {
                        var id = $(this).attr('data-id');

                        $table.bootstrapTable('remove', {
                            field: 'ID',
                            values: [id]
                        });
                        $table.bootstrapTable('refreshOptions', {
                            silent: true // Ensures no data is fetched again, just updates the UI
                        });
                    }
                })
            }
        })
    }, 0)
});


async function fetchRequest(method, action, auth, formData) {
    const options = {
        method: method,
    };

    const myHeaders = new Headers();
    if (auth) {
        myHeaders.append('Authorization', 'Bearer ' + localStorage.getItem("token"));
    }
    options.headers = myHeaders;

    if (formData !== undefined && ['POST', 'PUT', 'PATCH'].indexOf(method) !== -1) {
        options.body = new FormData(formData);
    }

    return await fetch(data.url + action, options)
        .then(response => response.json())
        .then(resData => {
            if (resData.status == 'authError') {
                logOut()
            }
            return resData;
        })
        .catch(error => console.error(error))
}

function loggedIn() {
    return localStorage.getItem("token") !== null;
}

function logOut() {
    localStorage.removeItem('token');
    window.location = '/';
}