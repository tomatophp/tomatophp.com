@livewireScripts
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

<!-- Meta Pixel Code -->
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ config('services.facebook.pixel_id') }}');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
               src="https://www.facebook.com/tr?id=1287269932419790&ev=PageView&noscript=1"
    /></noscript>
<!-- End Meta Pixel Code -->

@filamentSEO
