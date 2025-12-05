<script src="{{ asset('backend/custom_downloaded_file/axios.min.js') }}"></script>

{{-- JQUERY JS --}}
<script src="{{ asset('backend/plugins/jquery/jquery.min.js') }}"></script>

{{-- BOOTSTRAP JS --}}
<script src="{{ asset('backend/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('backend/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

{{-- SIDE-MENU JS --}}
<script src="{{ asset('backend/plugins/sidemenu/sidemenu.js') }}"></script>

{{-- Perfect SCROLLBAR JS --}}
<script src="{{ asset('backend/plugins/p-scroll/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('backend/plugins/p-scroll/pscroll.js') }}"></script>

{{-- STICKY JS --}}
<script src="{{ asset('backend/js/sticky.js') }}"></script>

{{-- INTERNAL Summernote Editor js --}}
<script src="{{ asset('backend/plugins/summernote-editor/summernote1.js') }}"></script>
<script src="{{ asset('backend/js/summernote.js') }}"></script>

{{-- dropify js --}}
<script src="{{ asset('backend/js/dropify.min.js') }}"></script>

{{-- toaster js --}}
<script src="{{ asset('backend/js/toastr.min.js') }}"></script>

{{-- DATA TABLE JS --}}
<script src="{{ asset('backend/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/js/butsns.bootstrap5.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/js/jszip.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/js/butsns.html5.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
<script src="{{ asset('backend/js/table-data.js') }}"></script>

{{-- APEXCHART JS --}}
<script src="{{ asset('backend/js/apexcharts.js') }}"></script>

{{-- INTERNAL SELECT2 JS --}}
<script src="{{ asset('backend/plugins/select2/select2.full.min.js') }}"></script>

{{-- FORM ELEMENTS JS --}}
<script src="{{ asset('backend/js/formelementadvnced.js') }}"></script>

{{-- CHART-CIRCLE JS --}}
<script src="{{ asset('backend/plugins/circle-progress/circle-progress.min.js') }}"></script>

{{-- INDEX JS --}}
<script src="{{ asset('backend/js/index1.js') }}"></script>
<script src="{{ asset('backend/js/index.js') }}"></script>

{{-- Reply JS --}}
<script src="{{ asset('backend/js/reply.js') }}"></script>

{{-- COLOR THEME JS --}}
<script src="{{ asset('backend/js/themeColors.js') }}"></script>

{{-- CUSTOM JS --}}
<script src="{{ asset('backend/js/custom.js') }}"></script>

{{-- SWITCHER JS --}}
<script src="{{ asset('backend/switcher/js/switcher.js') }}"></script>

{{-- SweetAlert2 JS --}}
<script src="{{ asset('backend/js/sweetalert2@11.js') }}"></script>

{{-- toastr start --}}
<script>
    $(document).ready(function() {
        toastr.options.timeOut = 10000;
        toastr.options.positionClass = 'toast-top-right';

        @if (Session::has('t-success'))
            toastr.options = {
            'closeButton': true,
            'debug': false,
            'newestOnTop': true,
            'progressBar': true,
            'positionClass': 'toast-top-right',
            'preventDuplicates': false,
            'showDuration': '1000',
            'hideDuration': '1000',
            'timeOut': '5000',
            'extendedTimeOut': '1000',
            'showEasing': 'swing',
            'hideEasing': 'linear',
            'showMethod': 'fadeIn',
            'hideMethod': 'fadeOut',
        };
        toastr.success("{{ session('t-success') }}");
        @endif

            @if (Session::has('t-error'))
            toastr.options = {
            'closeButton': true,
            'debug': false,
            'newestOnTop': true,
            'progressBar': true,
            'positionClass': 'toast-top-right',
            'preventDuplicates': false,
            'showDuration': '1000',
            'hideDuration': '1000',
            'timeOut': '5000',
            'extendedTimeOut': '1000',
            'showEasing': 'swing',
            'hideEasing': 'linear',
            'showMethod': 'fadeIn',
            'hideMethod': 'fadeOut',
        };
        toastr.error("{{ session('t-error') }}");
        @endif

            @if (Session::has('t-info'))
            toastr.options = {
            'closeButton': true,
            'debug': false,
            'newestOnTop': true,
            'progressBar': true,
            'positionClass': 'toast-top-right',
            'preventDuplicates': false,
            'showDuration': '1000',
            'hideDuration': '1000',
            'timeOut': '5000',
            'extendedTimeOut': '1000',
            'showEasing': 'swing',
            'hideEasing': 'linear',
            'showMethod': 'fadeIn',
            'hideMethod': 'fadeOut',
        };
        toastr.info("{{ session('t-info') }}");
        @endif

            @if (Session::has('t-warning'))
            toastr.options = {
            'closeButton': true,
            'debug': false,
            'newestOnTop': true,
            'progressBar': true,
            'positionClass': 'toast-top-right',
            'preventDuplicates': false,
            'showDuration': '1000',
            'hideDuration': '1000',
            'timeOut': '5000',
            'extendedTimeOut': '1000',
            'showEasing': 'swing',
            'hideEasing': 'linear',
            'showMethod': 'fadeIn',
            'hideMethod': 'fadeOut',
        };
        toastr.warning("{{ session('t-warning') }}");
        @endif
    });
</script>
{{-- toastr end --}}

{{-- dropify start --}}
<script>
    $(document).ready(function() {
        $('.dropify').dropify();

        $('#logo').on('dropify.afterClear', function(event, element) {
            $('input[name="remove_logo"]').val('1');
        });

        $('#favicon').on('dropify.afterClear', function(event, element) {
            $('input[name="remove_favicon"]').val('1');
        });
    });
</script>
{{-- dropify end --}}

{{-- summernot start --}}
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            tabsize: 2,
            height: 220,
        });
    });
</script>
<script src="https://www.gstatic.com/firebasejs/12.4.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging.js"></script>
{{-- summetnote end --}}
<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging.js";

// Firebase config
const firebaseConfig = {
  apiKey: "AIzaSyA5jd6I-e0ciYy3gyoiTz8Eq9f4PwEQsXs",
  authDomain: "mihreteabmesfun-fuyad.firebaseapp.com",
  projectId: "mihreteabmesfun-fuyad",
  storageBucket: "mihreteabmesfun-fuyad.firebasestorage.app",
  messagingSenderId: "750287104897",
  appId: "1:750287104897:web:a6b5c5b6f77758b47a9680",
  measurementId: "G-G0MNDTPSJC"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Request permission and get token
async function initFirebaseMessagingRegistration() {
  try {
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
      console.log('Notification permission denied');
      return;
    }

    const currentToken = await getToken(messaging, { 
      vapidKey: 'BHYDqCx-CB9icqwQRtiGP-dfaTVbpZtJbD3AbRIBGo_Y3Vbk363SLhHtSO9Cls_6BJ1PoPCRDEgAaU67cMmR5Fk' 
    });

    if (currentToken) {
      console.log('FCM Token:', currentToken);

      // Send token to backend
      await fetch('/save-fcm-token', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ token: currentToken })
      });
    } else {
      console.log('No registration token available.');
    }

  } catch (error) {
    console.error('FCM error:', error);
  }
}

initFirebaseMessagingRegistration();
</script>

@stack('scripts')
