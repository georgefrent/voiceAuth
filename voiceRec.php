<?php
session_start();
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Autentificare vocală</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
      body {
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        background-color: #f0f0f0;
      }

      h1 {
        color: #333;
      }

      button {
        margin: 10px;
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
      }

      button:disabled {
        background-color: #ccc;
        cursor: not-allowed;
      }

      #status {
        margin-top: 20px;
        font-size: 18px;
        color: #555;
      }
    </style>
  </head>
  <body>
    <h1>Autentificare vocală</h1><br>
    <p>Apăsați butonul „Începe înregistrarea”, rostiți cuvântul „Zero” în limba engleză și apoi apăsați butonul „Oprește înregistrarea”.</p>
    <p>Încercați să faceți înregistrarea cât mai scurtă posibil, de maxim 2 secunde.</p>
    <button id="startBtn" class="btn btn-primary">Începe înregistrarea</button>
    <button id="stopBtn" class="btn btn-danger" disabled>Oprește înregistrarea</button>
    <button id="retryBtn" class="btn btn-primary" disabled>Încearcă din nou</button>
    <button id="uploadBtn" class="btn btn-success" disabled>Încarcă înregistrarea</button>
    <div id="status">Gata de înregistrare</div>
    <div id="json"></div>
    <div id="jsonProb"></div>
    <div id="false"></div>
    <audio id="audioPreview" controls hidden></audio>
    <div id="success"></div>

    <script>
      const userName = <?php echo json_encode($username); ?>;

      let mediaRecorder;
      let audioChunks = [];
      let audioBlob;

      const delay = ms => new Promise(res => setTimeout(res, ms));
      const startBtn = document.getElementById("startBtn");
      const stopBtn = document.getElementById("stopBtn");
      const statusDiv = document.getElementById("status");
      const jsonDiv = document.getElementById("json");
      const jsonProbDiv = document.getElementById("jsonProb");
      const falseDiv = document.getElementById("false");
      const audioPreview = document.getElementById("audioPreview");
      const retryBtn = document.getElementById("retryBtn");
      const uploadBtn = document.getElementById("uploadBtn");
      const successDiv = document.getElementById("success");

      startBtn.addEventListener("click", async () => {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({
            audio: true,
          });
          mediaRecorder = new MediaRecorder(stream);

          mediaRecorder.ondataavailable = (event) => {
            audioChunks.push(event.data);
          };

          mediaRecorder.onstop = () => {
            audioBlob = new Blob(audioChunks, { type: "audio/webm" });
            audioChunks = [];

            const audioURL = URL.createObjectURL(audioBlob);
            audioPreview.src = audioURL;
            audioPreview.hidden = false;

            statusDiv.textContent = "Înregistrare completă. Examinați sunetul.";
            retryBtn.disabled = false;
            uploadBtn.disabled = false;

          };

          mediaRecorder.start();
          statusDiv.textContent = "Înregistrez...";
          startBtn.disabled = true;
          stopBtn.disabled = false;
          retryBtn.disabled = true;
          uploadBtn.disabled = true;
          audioPreview.hidden = true;

        } catch (error) {
          console.error("Eroare la accesarea microfonului:", error);
          statusDiv.textContent = "Accesul la microfon este interzis.";
        }
      });

      stopBtn.addEventListener("click", () => {
        mediaRecorder.stop();
        statusDiv.textContent = "Procesare...";
        startBtn.disabled = true;
        stopBtn.disabled = true;
      });

      retryBtn.addEventListener("click", () => {
        audioBlob = null;
        audioPreview.hidden = true;
        statusDiv.textContent = "Gata să înregistrez din nou.";
        retryBtn.disabled = true;
        uploadBtn.disabled = true;
        falseDiv.textContent = "";
        startBtn.disabled = false;
        jsonDiv.textContent = "";
        jsonProbDiv.textContent = "";
      });

      uploadBtn.addEventListener("click", async () => {
        if (!audioBlob) {
          statusDiv.textContent = "Niciun fișier audio de încărcat.";
          return;
        }
        
        const formData = new FormData();
            formData.append("audio", audioBlob, `${Date.now()}.webm`);

            statusDiv.textContent = "Încărcare...";

        try {
              const response = await fetch(
                `http://127.0.0.1:8000/${userName}/audio`,
                {
                  method: "POST",
                  body: formData,
                }
              );

              if (response.ok) {
                statusDiv.textContent = "Încărcat cu succes!";
              } else {
                statusDiv.textContent = "Încărcarea a eșuat.";
              }

              const jsonData = await response.json();

              jsonDiv.textContent = jsonData.is_same;
              jsonProbDiv.textContent = jsonData.prob_is_same;

              if (jsonData.is_same == "True"){
                successDiv.textContent = "Autentificare realizată cu succes"
                await delay(10000);
                window.location.replace("http://localhost/TMP/authToken.php");
              } else {
                falseDiv.textContent = "Autentificarea vocală a eșuat. Incercați din nou."
              }

            } catch (error) {
              console.error("Eroare la încărcarea fișierului audio:", error);
              statusDiv.textContent = "Încărcarea a eșuat.";
            }
        });

    </script>
  </body>
</html>

<?php
  require('config.php');

?>

