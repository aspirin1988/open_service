<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{asset('js/uikit.js')}}"></script>
<script src="{{asset('js/components/notify.js')}}"></script>
<script src="{{asset('js/components/datepicker.js')}}"></script>
<script src="{{asset('js/components/timepicker.js')}}"></script>
<script src="{{asset('js/components/autocomplete.js')}}"></script>
<script src="{{asset('js/angular/angular.js')}}"></script>
<script src="{{asset('js/angular/app-angular.js')}}"></script>
<script>
    var showContent = function ()
    {
        $('[ng-controller]').show();
        $('[role="progressbar"]').hide();
    };

    document.addEventListener("DOMContentLoaded" , function ()
    {
        setTimeout(showContent , 0);
    });
</script>