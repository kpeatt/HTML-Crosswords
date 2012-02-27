<script type="text/javascript">
    $(function(){
    
    	$('#puzzle td').height($('#puzzle td').width());
    	
        var answerKey = "<?php echo $puzzle['answerstring']; ?>";
         
        $('input.answer').blur(function() {
             
            var currentCell = $(this).attr('id').substr(5);
            var response = $(this).val().toLowerCase();
            var answer = answerKey.charAt(currentCell-1).toLowerCase();
             
            console.log(response);
             
            console.log(answer);
             
            if (response === answer) {
                $(this).removeClass('wrong').addClass('right');
            } else if (response === '') {
                $(this).removeClass('wrong').removeClass('right');
            } else {
                $(this).removeClass('right').addClass('wrong');
            }
             
        });
    });
    
    $(window).resize(function() {
    	$('#puzzle td').height($('#puzzle td').width());
    });
    
</script>