document.addEventListener('DOMContentLoaded', function(){
    let selects  = document.querySelectorAll("[select-type='filter']");
    selects.forEach(function(select){
        select.addEventListener('change', function(){
            let form = this.parentNode;
            form.submit();
        });
    });
});