@extends('layouts.app')

@section('title', __('message.title.profile'))

@section('content')
    <div class="content" id="content">
        <h1>@lang('message.h1.profile')</h1>

        <form method="post" action="{{ route('upload.xml') }}" class="dropzone" id="xml-upload-form"
            enctype="multipart/form-data">
            @csrf
        </form>

        <span id="upload-progress-text" class="progress-text">0 / 0</span>
        <div id="upload-progress-bar" class="progress-bar">
            <div id="upload-progress-fill" class="progress-fill">
            </div>
        </div>

        <div class="profile-image-container">
            <img id="profile-image" src="{{ asset('storage/' . Auth::user()->image) }}" alt="Profile Image"
                class="profile-image">
        </div>
        <div class="form-container profil-form with-deco">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div id="names_fields">
                    <input type="text" name="first_name" placeholder="Prénom"
                        value="{{ old('first_name', Auth::user()->first_name) }}">
                    <input type="text" name="name" placeholder="Nom" value="{{ old('name', Auth::user()->name) }}">
                </div>
                <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}">
                <p onclick="showPasswordField()">
                    <img id="password_fields_img" src="{{ asset('images/icons8-right-arrow-96.png') }}" alt="">
                    Pour changer votre mot de passe, déroulez vers le bas :
                </p>
                <div id="password_fields">
                    <input type="password" name="current_password" placeholder="Mot de passe actuel">
                    <input type="password" name="new_password" placeholder="Nouveau mot de passe">
                    <input type="password" name="new_password_confirmation" placeholder="Confirmer le nouveau mot de passe">
                </div>
                <div id="register-file-upload-container">
                    <label for="file-upload">
                        Sélectionner une photo de profil
                    </label>
                    <input id="file-upload" type="file" name="image">
                    <span id="file-name">Aucun fichier choisi</span>
                </div>
                <div class="form-actions">
                    <button type="submit">Mettre à jour</button>
                    <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('file-upload').addEventListener('change', function (event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profile-image').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        var myDropzone = new Dropzone(".dropzone", {
            url: "{{ route('upload.xml') }}",
            uploadMultiple: true,
            paramName: "xml_files[]",
            parallelUploads: 25,
            maxFilesize: 10,
            acceptedFiles: ".xml",
            dictInvalidFileType: "Seuls les fichiers XML sont autorisés.",
        });


        let uploadCheckInterval = null;

        function startUploadProgressCheck() {
            const progressBar = document.getElementById('upload-progress-bar');
            const progressFill = document.getElementById('upload-progress-fill');
            const progressText = document.getElementById('upload-progress-text');

            progressBar.style.display = 'block';
            progressText.style.display = 'block';

            uploadCheckInterval = setInterval(() => {
                fetch("{{ route('upload.progress') }}")
                    .then(response => response.json())
                    .then(data => {
                        const { upload_progress, upload_total } = data;

                        if (upload_progress !== null && upload_total) {
                            const percentage = Math.round((upload_progress / upload_total) * 100);
                            progressFill.style.width = percentage + '%';

                            // Mettre à jour le texte
                            progressText.textContent = `${upload_progress} / ${upload_total}`;

                            if (upload_progress >= upload_total) {
                                clearInterval(uploadCheckInterval);
                                uploadCheckInterval = null;

                                setTimeout(() => {
                                    progressBar.style.display = 'none';
                                    progressFill.style.width = '0%';
                                    progressText.textContent = '0 / 0';
                                    progressText.style.display = 'none';
                                }, 1000);
                            }
                        }
                    })
                    .catch(error => {
                        console.error("Erreur de progression :", error);
                    });
            }, 500);
        }

        myDropzone.on("sending", function (file) {
            if (uploadCheckInterval === null && file.name.endsWith('.xml')) {
                startUploadProgressCheck();
            }
        });
    </script>
@endsection