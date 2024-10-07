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



<script>
    $(function(){  // $(document).ready shorthand
// get the list of all highlight code blocks
        const highlights = document.querySelectorAll(".shiki");

        highlights.forEach((pre) => {
            if(pre.classList.length >0){
                // create the copy button
                const copy = document.createElement("button");
                copy.innerHTML = "Copy";

                copy.classList.add("relative");
                copy.classList.add("float-right");
                copy.classList.add("text-xs");
                copy.classList.add("font-semibold");
                copy.classList.add("text-white");
                copy.classList.add("bg-gray-800");
                copy.classList.add("hover:bg-gray-600");
                copy.classList.add("rounded-md");
                copy.classList.add("px-2");
                copy.classList.add("py-1");
                copy.classList.add("mt-1");
                // add the event listener to each click
                copy.addEventListener("click", handleCopyClick);
                // append the copy button to each code block
                pre.prepend(copy);
            }

        });

        const copyToClipboard = (str) => {
            const el = document.createElement("textarea"); // Create a <textarea> element
            el.value = str; // Set its value to the string that you want copied
            el.setAttribute("readonly", ""); // Make it readonly to be tamper-proof
            el.style.position = "absolute";
            el.style.left = "-9999px"; // Move outside the screen to make it invisible
            document.body.appendChild(el); // Append the <textarea> element to the HTML document
            const selected =
                document.getSelection().rangeCount > 0 // Check if there is any content selected previously
                    ? document.getSelection().getRangeAt(0) // Store selection if found
                    : false; // Mark as false to know no selection existed before
            el.select(); // Select the <textarea> content
            document.execCommand("copy"); // Copy - only works as a result of a user action (e.g. click events)
            document.body.removeChild(el); // Remove the <textarea> element
            if (selected) {
                // If a selection existed before copying
                document.getSelection().removeAllRanges(); // Unselect everything on the HTML document
                document.getSelection().addRange(selected); // Restore the original selection
            }
        };

        function handleCopyClick(evt) {
            // get the children of the parent element
            let innerText = evt.target.parentElement.textContent.replace('Copy', '');

            // copy all of the code to the clipboard
            copyToClipboard(innerText);
            // Change the button's text to "Copied"
            this.textContent = "Copied";
            // Change back the button text to "Copy" after 2 sec
            setTimeout(() => {
                this.textContent = "Copy";
            }, 2000);
        }
    });
</script>
