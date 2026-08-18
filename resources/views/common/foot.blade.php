		<!-- New Js start-->
		<script src="{{ asset('public/js/jquery-2.2.4.min.js') }}"></script>
		<script src="{{ asset('public/js/bootstrap.bundle.min.js') }}"></script>
		<!-- Guard against IndexSizeError when selection has no range -->
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
		<script src="{{ asset('public/js/main.min.js') }}"></script>

		

		<!-- New Js End -->
		<!-- Needed Js from Old Version Start-->
		<script type="text/javascript">
			var APP_URL = "{{ url('/') }}";
			var USER_ID = "{{ isset(Auth::user()->id)  ? Auth::user()->id : ''  }}";
			var sessionDate      = '{!! Session::get('date_format_type') !!}';
			var token = '{{ csrf_token() }}';
			var sessionLanguage      = '{!! Session::get('language') !!}';

		</script>
		<script src="{{ asset('public/js/front-foot.min.js') }}"></script>
		<script>
            'use strict';
                if (sessionLanguage === 'ar') {
                    document.documentElement.setAttribute("dir", "rtl");
                } else {
                    document.documentElement.setAttribute("dir", "ltr");
                }
        </script>

		<!-- Needed Js from Old Version End -->
		@stack('scripts')
	</body>
</html>