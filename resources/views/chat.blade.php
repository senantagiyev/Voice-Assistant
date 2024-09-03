<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Voice Assistant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #7b9fc9;
            font-family: Arial, sans-serif;
        }
        .container {
            background: #ffffff; /* Beyaz arka plan rengi */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2); /* Hafif gölge efekti */
            padding: 15px;
            margin-top: 100px;
            max-width: 400px; /* Maksimum genişlik */
        }
        h1 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #333;
        }
        #dialog {
            font-family: Arial, sans-serif;
            /*border: 1px solid #ddd;*/
            padding: 8px;
            /*background: #f8f9fa;*/
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .user, .assistant {
            margin-bottom: 8px;
            font-size: 0.85rem;
        }
        .user {
            color: #ffffff;
        }
        .assistant {
            color: #ffffff;
        }
        .dialog-item {
            margin-bottom: 4px;
        }
        .btn-start {
            font-size: 1rem;
            padding: 8px 16px;
            border-radius: 5px;
            background: #007bff;
            color: #ffffff;
            border: none;
            outline: none;
            transition: background 0.3s ease;
        }
        .btn-start:hover {
            background: #0056b3;
        }

        .e-card {
            margin: 100px auto;
            background: transparent;
            box-shadow: 0px 8px 28px -9px rgba(0,0,0,0.45);
            position: relative;
            width: 400px;
            height: max-content;
            border-radius: 16px;
            overflow: hidden;
        }

        .wave {
            position: absolute;
            width: 540px;
            height: 700px;
            opacity: 0.6;
            left: 0;
            top: 0;
            margin-left: -50%;
            margin-top: -70%;
            z-index: -1;
            background: linear-gradient(744deg, #9f54e0, #169367 60%,#00ddeb);
        }

        .icon {
            width: 3em;
            margin-top: -1em;
            padding-bottom: 1em;
        }

        .infotop {
            text-align: center;
            font-size: 20px;
            position: absolute;
            top: 5.6em;
            left: 0;
            right: 0;
            color: rgb(255, 255, 255);
            font-weight: 600;
            z-index: -1;
        }

        .name {
            font-size: 14px;
            font-weight: 100;
            position: relative;
            top: 1em;
            text-transform: lowercase;
        }

        .wave:nth-child(2),
        .wave:nth-child(3) {
            top: 210px;
        }

        .playing .wave {
            border-radius: 40%;
            animation: wave 3000ms infinite linear;
        }

        .wave {
            border-radius: 40%;
            animation: wave 55s infinite linear;
        }

        .playing .wave:nth-child(2) {
            animation-duration: 4000ms;
        }

        .wave:nth-child(2) {
            animation-duration: 50s;
        }

        .playing .wave:nth-child(3) {
            animation-duration: 5000ms;
        }

        .wave:nth-child(3) {
            animation-duration: 45s;
        }

        @keyframes wave {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .main{
            z-index: 999!important;
        }
    </style>
</head>
<body>
<div class="container  e-card playing">
    <div class="image"></div>

    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="infotop">

        <br>
    </div>



@if(isset($question))
    <p style="color: #fff">
        Men : {{ $question }}
    </p>

    <p style="color: #fff">
        Cavab : {{ $answer }}
    </p>
@endif

<div class="main">
    <form  action="{{ route('ask') }}" method="POST">
        @csrf
        <input autocomplete="off" class="form form-control" type="text" name="question" placeholder="Yazin...">
        <br>
        <button class="btn btn-primary">Sorus</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.getElementById('start').onclick = function() {
        const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'tr-TR';

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;

            const dialog = document.getElementById('dialog');
            const userMessage = document.createElement('div');
            userMessage.className = 'user dialog-item';
            userMessage.textContent = 'User: ' + transcript;
            dialog.appendChild(userMessage);

            fetch('/voice-command', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ command: transcript })
            }).then(response => response.json())
                .then(data => {
                    const msg = new SpeechSynthesisUtterance();
                    msg.text = data.message;
                    msg.lang = 'tr-TR';

                    const voices = window.speechSynthesis.getVoices();
                    const femaleVoice = voices.find(voice => voice.lang === 'tr-TR' && voice.name.includes('Female'));
                    if (femaleVoice) {
                        msg.voice = femaleVoice;
                    }

                    window.speechSynthesis.speak(msg);

                    const assistantMessage = document.createElement('div');
                    assistantMessage.className = 'assistant dialog-item';
                    assistantMessage.textContent = 'Assistant: ' + data.message;
                    dialog.appendChild(assistantMessage);

                    if (data.url) {
                        window.open(data.url, '_blank');
                    }

                    dialog.scrollTop = dialog.scrollHeight;
                });
        };

        recognition.start();
    };
</script>
</body>
</html>
