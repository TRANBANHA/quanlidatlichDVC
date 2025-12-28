 <!--   Core JS Files   -->
 <script src="{{ asset('js') }}/core/jquery-3.7.1.min.js"></script>
 <script src="{{ asset('js') }}/core/popper.min.js"></script>
 <script src="{{ asset('js') }}/core/bootstrap.min.js"></script>

 <!-- Select2 -->
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

 <!-- jQuery Scrollbar -->
 <script src="{{ asset('js') }}/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

 <!-- Chart JS -->
 <script src="{{ asset('js') }}/plugin/chart.js/chart.min.js"></script>

 <!-- jQuery Sparkline -->
 <script src="{{ asset('js') }}/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

 <!-- Chart Circle -->
 <script src="{{ asset('js') }}/plugin/chart-circle/circles.min.js"></script>

 <!-- Datatables -->
 <script src="{{ asset('js') }}/plugin/datatables/datatables.min.js"></script>

 <!-- Bootstrap Notify -->
 <script src="{{ asset('js') }}/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>


 <!-- Sweet Alert -->
 <script src="{{ asset('js') }}/plugin/sweetalert/sweetalert.min.js"></script>

 <!-- Kaiadmin JS -->
 <script src="{{ asset('js') }}/kaiadmin.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
 <script>
     $(document).ready(function() {
         $('.select2').select2({
             placeholder: 'Chọn cán bộ',
             allowClear: true
         });
     });
 </script>
 @yield('scripts')
 @stack('scripts')
