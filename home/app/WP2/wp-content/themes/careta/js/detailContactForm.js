jQuery(function($){
    $("#detailContactForm").validate({
        rules:{
            "cf_name":{
                required:true
            },
            "cf_email":{
                required:true,
                email:true
            },
            "cf_tel":{
                required:true
            }
        },
        messages:{
            "cf_name":{
                required:"お名前を入力してください。"
            },
            "cf_email":{
                required:"メールアドレスを入力してください。",
                email:"メールアドレスを正しく入力してください。"
            },
            "cf_tel":{
                required:"電話番号を入力してください。"
            }
        },
        validClass:"cfValid",
        errorClass:"cfInvalid"
    });

    $("#detailContactForm").submit(function(event){
        event.preventDefault();

        if(!$("#detailContactForm").valid()) {
            window.alert('必須項目を正しく入力してください。');
            return false;
        }

        if(!confirm('送信します。よろしいでしょうか？')){
            return false;
        }
        $("#detailContactForm").attr('action', 'http://www.takahama428.com/app/WP2/wp-content/themes/careta/cf_execute.php');
        var $form = $("#detailContactForm");
        var $button = $('#btnSubmit');

        $.ajax({
            url: 'http://www.takahama428.com/app/WP2/wp-content/themes/careta/cf_execute.php',
            type: 'post',
            data:$form.serialize(),
            timeout: 10000,
            beforeSend:function(){
                $button.attr('disabled', true);
            },
            success:function(response){
            },
            complete:function(response){
//console.log('[complete]'+response.responseText);
                if(response.responseText==1) {
                    window.alert('お問い合わせを受け付けました。');
                    $("#cf_name").val('');
                    $("#cf_email").val('');
                    $("#cf_tel").val('');
                    $("#cf_category").val(0);
                    $("#cf_destination").val(0);
                    $("#cf_delivery_date").val('');
                    $("#cf_message").val('');
                }else{
                    window.alert('メール送信処理でエラーが発生しました。['+response.responseText+']');
                }
                $button.attr('disabled', false);
            },
            error:function(response){
//console.log('[error]'+response.responseText);
                window.alert('通信エラーが発生しました。');
                $button.attr('disabled', false);
            }
        });

        return false;
    });
});
