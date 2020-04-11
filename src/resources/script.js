

function translate(name) {
    if (translations[name]) {
        return translations[name];
    }

    return name;
}

function ready() {
    $('#resetcache').submit(function(e) {
        e.preventDefault();
        if (confirm(translate('Are you sure? All cached classes data will be removed'))) {
            $.ajax({
                method: "POST",
                url: location.href,
                data: {resetcache: 1}
            }).done(function() {
                location.reload();
            });
        }
    });
}
