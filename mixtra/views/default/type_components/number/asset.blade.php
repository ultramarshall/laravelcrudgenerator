@push('bottom')
	<script type="text/javascript">
        function numberToString(text, decimals) {
            text = text.toString().replace(/,/g, '');
            var number = parseFloat(Math.round(text * 100) / 100).toFixed(decimals);
            
            var parts = number.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    		return parts.join(".");
            
            // return $.number(number, 2, '.', ',');
            // return number.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        }
	</script>
@endpush
