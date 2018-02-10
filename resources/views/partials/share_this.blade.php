<?php $pageUrl = Request()->url(); ?>
<div class="share-this no-print">
    <div class="row">
        <div class="col-sm-6 hidden-xs">
            @lang("Share this story")
        </div>
        <div class="col-md-6 text-right">

            <a data-toggle="tooltip" title="Send email"
               href="mailto:?Subject=Checkout this item!&amp;Body=I%20saw%20this%20and%20thought%20you%20might%20like%20it!%20 {{$pageUrl}}">
                <i class="fa fa-inbox"></i>
            </a>

            <a data-toggle="tooltip" title="Facebook" href="http://www.facebook.com/sharer.php?u={{$pageUrl}}"
               target="_blank">
                <i class="fa fa-facebook-square"></i>
            </a>

            <a data-toggle="tooltip" title="Twitter"
               href="https://twitter.com/share?url={{$pageUrl}}&amp;text={{$title}}&amp;hashtags=amdtllc"
               target="_blank">
                <i class="fa fa-twitter-square"></i>
            </a>

            <a data-toggle="tooltip" title="Google Plus" href="https://plus.google.com/share?url={{$pageUrl}}"
               target="_blank">
                <i class="fa fa-google-plus-square"></i>
            </a>
            <a data-toggle="tooltip" title="Linked-In"
               href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{$pageUrl}}" target="_blank">
                <i class="fa fa-linkedin-square"></i>
            </a>

            <a data-toggle="tooltip" title="Pintrest"
               href="javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());">
                <i class="fa fa-pinterest"></i>
            </a>

            <a data-toggle="tooltip" title="Tumblr"
               href="http://www.tumblr.com/share/link?url={{$pageUrl}}&amp;title={{$title}}"
               target="_blank">
                <i class="fa fa-tumblr-square"></i>
            </a>
            <a data-toggle="tooltip" title="Print" href="javascript:;" onclick="window.print()">
                <i class="fa fa-print"></i>
            </a>

        </div>

    </div>
</div>

<script>
    window.fbAsyncInit = function () {
        FB.init({
            appId: '621906377978527',
            xfbml: true,
            version: 'v2.7'
        });
    };

    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<div
        class="fb-like no-print"
        data-share="true"
        data-width="450"
        data-show-faces="true">
</div>
<p></p>