//Вывод crud панелей
function getUsers() {
    console.log("getUsers");
    $.ajax({
        method: 'POST',
        url: "/admin_panel/user"
    }).done(function (data) {
        $('.js-get-users').html(data);
    });
}
function getDocuments() {
    console.log("getDocuments");
    $.ajax({
        method: 'POST',
        url: "/admin_panel/document"
    }).done(function (data) {
        $('.js-get-docs').html(data);
    });
}
function getTags() {
    console.log("getTags");
    $.ajax({
        method: 'POST',
        url: "/admin_panel/tag"
    }).done(function (data) {
        $('.js-get-tags').html(data);
    });
}

//вызов формы добавления
function getAddForm(add) {

    console.log(add);
    $.ajax({
        method: 'POST',
        url: "/admin_panel/"+add
    }).done(function (data) {
        $('.js-form').html(data);
    });
}
//отправка формы
//Переделать запросы ajax с FormData, вместо Serialize
function sendForm(){
    let form =  document.querySelector('form');
    console.dir(form.name);
    let msg = $( "form" ).serialize();
    if(form.name == "add_user_form"){
    $.ajax({
        method: 'POST',
        url: "/admin_panel/add_user",
        data: msg
    }).done(getUsers());
    }else if(form.name == "edit_user_form"){
    let id = form.querySelector('h1').innerText;
    console.dir(id);
    $.ajax({
        method: 'POST',
        url: "/admin_panel/edit_user/"+id,
        data: msg
    }).done(getUsers());
    }else if(form.name == "add_tag_form"){
        console.dir(form.name);
        var formElement = document.querySelector("form");
        console.dir(formElement);
        var kekw = new FormData(formElement);
    $.ajax({
        method: 'POST',
        url: "/admin_panel/add_tag",
        cache: false,
    contentType: false,
    processData: false,
    data: kekw,
    dataType : 'json'
    }).done(getTags());
    }else if(form.name == "add_document_form"){
        var form1 = $( "form" );
        var formElement = document.querySelector("form");
        console.dir(formElement);
        var kekl = new FormData(formElement);
        $.ajax({
            method: 'POST',
            url: "/admin_panel/add_doc",
            cache: false,
            contentType: false,
            processData: false,
            data: kekl,
            dataType : 'json',
    }).done(getDocuments());
    }
}