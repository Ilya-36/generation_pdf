<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Генератор PDF из изображений</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: sans-serif;
        }

        #image-upload {
            margin-bottom: 20px;
        }

        #uploaded-images {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        #uploaded-images img {
            margin: 10px;
            max-width: 200px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Генерация PDF-файла</h1>

        <div id="image-upload">
            <h2>Загрузите изображения:</h2>
            <input type="file" id="image-input" accept="image/jpeg, image/png" multiple>
            <div id="uploaded-images"></div>
        </div>

        <button id="generate-pdf" disabled>Сгенерировать PDF</button>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        const imageInput = document.getElementById('image-input');
        const uploadedImages = document.getElementById('uploaded-images');
        const generatePdfButton = document.getElementById('generate-pdf');

        imageInput.addEventListener('change', (event) => {
            const files = event.target.files;

            for (const file of files) {
                if (file.type === 'image/jpeg' || file.type === 'image/png' && file.size <= 5242880) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        uploadedImages.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Неверный формат файла или размер');
                }
            }

            if (uploadedImages.children.length > 0) {
                generatePdfButton.disabled = false;
            }
        });

        generatePdfButton.addEventListener('click', () => {
            const images = Array.from(uploadedImages.children);
            const imageUrls = images.map(img => img.src);
            const randomImages = shuffle(imageUrls);

            fetch('/generate-pdf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ images: randomImages })
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'images.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });

        function shuffle(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }
    </script>
</body>
</html>
