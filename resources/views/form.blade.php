<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row mt-4">
            <div class="col-6 shadow p-4">
                <h1>Compose Message</h1>
                <hr>

                <form id="FormIdentifier" action="{{ url("") }}">
                    {{ csrf_field() }}
                    <div class="mb-3">
                        <label for="senderIDidentifier" class="form-label">Sender ID</label>
                        <input type="text" class="form-control" id="senderIDidentifier" name="senderid" aria-describedby="senderIdHelp">
                        <div id="senderIdHelp" class="form-text">Your sender ID must be between 3 to 11 characters.</div>
                        </div>

                        <div class="mb-3">
                            <label for="recipientIdentifier" class="form-label">Recipient</label>
                            <textarea class="form-control" id="recipientIdentifier" rows="3" name="recipient"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="messageIdentifier" class="form-label">Message</label>
                            <textarea class="form-control" id="messageIdentifier" rows="3" aria-describedby="messageHelp" name="message"></textarea>
                            <p>Characters Count: <span id="charCount">0</span></p>
                            <div id="messageHelp" class="form-text">
                            </div>
                            <input type="hidden" name="page_count" id="pageCountIdentifier" value="0">
                        </div>

                        <button type="submit" class="btn btn-primary">Send Message</button>

                </form>
            </div>
            <div id="summaryIdentifier" class="col-6 shadow p-4">
                <h1>Summary</h1>
                <hr>
                <div id="summarize">
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-W8fXfP3gkOKtndU4JGtKDvXbO53Wy8SZCQHczT5FMiiqmQfUpWbYdTil/SxwZgAN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>

    <script type="text/javascript">
        const charCount = document.querySelector('#charCount');
        const pagesMessage = document.querySelector('#messageHelp');
        const pageIden = document.querySelector('#pageCountIdentifier');
        const summary = document.querySelector('#summarize');

        function generatePagesCount(counter, Left){
            let pages_str = "";
            let amountLeft = 0;
            for(let i = 0; i < counter; i++){
                if(i == (counter - 1)) amountLeft = Left;
                pages_str += `<p>Pages: ${i + 1}, You have ${amountLeft} characters left on this page.</p>`;
            }
            pagesMessage.innerHTML = pages_str;
            pageIden.value = counter;
        }

        function countChars(){
            const re = /./g;
            let str = textBox.value;
            return ((str || '').match(re) || []).length
        }

        function handleCharactersCount(event){
            event.preventDefault();
            const currentCharacters = countChars();
            const currentPageCount = (currentCharacters <= 160) ? 1 : Math.ceil((currentCharacters - 160) / 154) + 1;
            let upperLimit = 160 + (currentPageCount - 1) * 154;
            const charLeft = upperLimit - currentCharacters;
            generatePagesCount(currentPageCount, charLeft);
            charCount.innerHTML = currentCharacters;
        }

        const textBox = document.querySelector('#messageIdentifier');
        textBox.addEventListener('keyup', handleCharactersCount, false);

        function handleFormSubmit(event) {
            event.preventDefault();

            const form = event.currentTarget;
            const url = form.action;

            try {
                const formData = new FormData(form);
                const plainFormData = Object.fromEntries(formData.entries());
                const formDataJsonString = JSON.stringify(plainFormData);

                const fetchOptions = {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: formDataJsonString,
                };

                fetch(url, fetchOptions)
                .then( res => res.json())
                .then( data => summary.innerHTML = JSON.stringify(data, null, 2) );
            } catch (error) {
                summary.innerHTML = error;
            }
        }

        const form = document.querySelector('#FormIdentifier');
        form.addEventListener("submit", handleFormSubmit, false);
    </script>
</body>
</html>
