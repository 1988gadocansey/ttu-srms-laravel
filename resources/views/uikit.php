<?php
/**
 * Created by PhpStorm.
 * User: gadoo
 * Date: 12/06/2019
 * Time: 2:54 PM
 */?>
<html>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/css/uikit.css">

 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/js/uikit.js"></script>




<body>

<div class="js-upload uk-placeholder uk-text-center">
    <span uk-icon="icon: cloud-upload"></span>
    <span class="uk-text-middle">Attach binaries by dropping them here or</span>
    <div uk-form-custom>
        <input type="file" multiple>
        <span class="uk-link">selecting one</span>
    </div>
</div>

<progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>


</body>
<script type="text/javascript">
    var bar = document.getElementById('js-progressbar');

    UIkit.upload('.js-upload', {

        //url: '/echo/json/',
        'url': '/echo/htmdl/',
        'data-type': 'json',
        'name': 'json',
        'multiple': false,
        'params': {
            'json': '{"name":"John","age":30,"car":null}',
            'html': '<p>this is html</p>'
        },

        beforeSend: function() {
            console.log('beforeSend', arguments);
        },
        beforeAll: function() {
            console.log('beforeAll', arguments);
        },
        load: function() {
            console.log('load', arguments);
        },
        error: function() {
            console.log('error', arguments);
        },
        complete: function() {
            console.log('complete', arguments);
        },

        loadStart: function(e) {
            console.log('loadStart', arguments);

            bar.removeAttribute('hidden');
            bar.max = e.total;
            bar.value = e.loaded;
        },

        progress: function(e) {
            console.log('progress', arguments);

            bar.max = e.total;
            bar.value = e.loaded;
        },

        loadEnd: function(e) {
            console.log('loadEnd', arguments);

            bar.max = e.total;
            bar.value = e.loaded;
        },

        completeAll: function(HttpRequest) {
            console.log('completeAll', arguments);

            setTimeout(function() {
                bar.setAttribute('hidden', 'hidden');
            }, 1000);

            console.log('Upload Completed');
            console.log('Response', HttpRequest.response);

        }

    });

</script>
</html>