@livewireScripts
@livewire(\App\Livewire\FcmToken::class)

@livewire('notifications')
@filamentScripts
<script src="{{ asset('/js/jquery-3.7.1.min.js') }}"></script>
<script>
    $(function(){  // $(document).ready shorthand
        $('.app').fadeIn('slow');
    });
</script>

<script type="module">
    function attachEvent(selector, event, fn) {
        const matches = document.querySelectorAll(selector);
        if (matches?.length > 0) {
            for (const element of matches) {
                element.addEventListener(event, () => {
                    fn(element);
                }, false);
            }
        }
    }
    window.onload = () => {
        attachEvent('[data-toggle-menu]', 'click', element => {
            element.classList.toggle('expanded');
            document.body.classList.toggle('overflow-hidden');
            document.getElementById('menu')?.classList.toggle('hidden');
        });

    };
    window.onpageshow = () => {
        const element = document.querySelector('[data-toggle-menu]');
        if (element) {
            element.classList.remove('expanded');
        }
        document.body.classList.remove('overflow-hidden');
        document.getElementById('menu')?.classList.add('hidden');
    };

</script>


@vite('resources/js/app.js')
@stack('js')

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MJS9LZXW"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-LBY5VLXL0V"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-LBY5VLXL0V');
</script>

<script>
    window.axeptioSettings = {
        clientId: "66fa9fa9d69759fcd582183f",
        cookiesVersion: "tomatophp-en-EU",
        googleConsentMode: {
            default: {
                analytics_storage: "denied",
                ad_storage: "denied",
                ad_user_data: "denied",
                ad_personalization: "denied",
                wait_for_update: 500
            }
        }
    };

    (function(d, s) {
        var t = d.getElementsByTagName(s)[0], e = d.createElement(s);
        e.async = true; e.src = "//static.axept.io/sdk.js";
        t.parentNode.insertBefore(e, t);
    })(document, "script");
</script>
