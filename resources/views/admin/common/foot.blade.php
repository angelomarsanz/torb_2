<script type="text/javascript">
  var APP_URL = "{{ (url('/')) }}";
</script>
<!-- jQuery 3.6 -->
<script type="text/javascript" src="{{ asset('public/backend/plugins/jQuery/jquery-3.6.3.min.js') }}"></script>
<!-- Popper.js -->
<script type="text/javascript" src="{{ asset('public/backend/bootstrap/js/popper.min.js') }}"></script>
<!-- Bootstrap Slim -->
<script type="text/javascript" src="{{ asset('public/backend/bootstrap/js/slim.min.js') }}"></script>
<!-- jQuery Validation -->
<script type="text/javascript" src="{{ asset('public/backend/plugins/jQuery/jquery.validate.min.js') }}"></script>
<!-- jQuery UI -->
<script type="text/javascript" src="{{ asset('public/backend/plugins/jQueryUI/jquery-ui.min.js') }}"></script>
<script type="text/javascript">
(function(){
  if (typeof window.getSelection === 'undefined') return;
  var proto = window.Selection && window.Selection.prototype;
  if (!proto || !proto.getRangeAt) return;
  var getRangeAt = proto.getRangeAt;
  proto.getRangeAt = function(index) {
    if (this.rangeCount === 0) {
      var range = document.createRange();
      range.setStart(document.body, 0);
      range.collapse(true);
      return range;
    }
    return getRangeAt.call(this, index);
  };
})();
</script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script type="text/javascript">
  $.widget.bridge('uibutton', $.ui.button);
  var sessionDate      = '{!! Session::get('date_format_type') !!}';
</script>
<!-- Bootstrap 5 Bundle -->
<script type="text/javascript" src="{{ asset('public/backend/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script type="text/javascript">
  function onGoogleMapsReady() {
    window.googleMapsReady = true;
    var s = document.createElement('script');
    s.src = "{{ asset('public/backend/js/locationpicker.jquery.min.js') }}";
    s.async = false;
    document.head.appendChild(s);
    s.onload = function() {
      if (typeof window.onLocationPickerReady === 'function') {
        window.onLocationPickerReady();
      }
    };
  }
</script>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key={{ config('vrent.google_map_key') }}&libraries=places&loading=async&callback=onGoogleMapsReady" async defer></script>
<script type="text/javascript" src="{{ asset('public/backend/js/bootbox.min.js') }}"></script>
<!-- AdminLTE 4 App -->
<script type="text/javascript" src="{{ asset('public/backend/dist/js/adminlte.min.js') }}"></script>
<!-- Admin JS -->
<script type="text/javascript" src="{{ asset('public/backend/dist/js/admin.min.js') }}"></script>
<!-- Backend JS -->
<script type="text/javascript" src="{{ asset('public/backend/js/backend.min.js') }}"></script>
<!-- Sparkline -->
<script type="text/javascript" src="{{ asset('public/backend/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap -->
<script type="text/javascript" src="{{ asset('public/backend/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- jQuery Knob Chart -->
<script type="text/javascript" src="{{ asset('public/backend/plugins/knob/jquery.knob.js') }}"></script>
<!-- Daterange picker -->
<script type="text/javascript">

    var separator  = '{{ settings("date_separator") }}';
    var dateFormat = '{{ strtoupper(settings("date_format_type")); }}';
    var splitDate  = dateFormat.split(separator);

    if (splitDate[1] === 'M') {
        dateFormat  = dateFormat.replace('M', 'MMM');
    }

</script>
<script type="text/javascript" src="{{ asset('public/backend/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<!-- WYSIHTML5 -->
<script type="text/javascript" src="{{ asset('public/backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<!-- Select2 -->
<script type="text/javascript" src="{{ asset('public/backend/plugins/select2/select2.full.min.js') }}"></script>
<!-- Custom JS -->
<script type="text/javascript" src="{{ asset('public/backend/dist/js/custom.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/js/daterangecustom.js') }}"></script>
@stack('scripts')
</body>
</html>
