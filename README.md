# Voice Authentication System 🔐🎤
This project implements a voice-based authentication system that enhances traditional password-based login with an additional layer of voice verification. It combines FastAPI, TensorFlow, and Librosa for processing and validating audio recordings. The frontend is built using HTML, CSS, and JavaScript, while PHP handles user authentication and database interactions. The user interface (UI) is fully implemented in Romanian because the project was made for a Romanian University class.

## How It Works
1. User Registration
- Users create an account with a username and password.
- They record their first voice sample, which is stored as a reference.
  
2. User Login
- Users enter their username and password.
- They are prompted to record a new voice sample.
- The system compares the new recording with the stored reference.
- If both the password and voice match, the user is authenticated.
  
3. Account Deletion
- Users can delete their account.
- This removes their username, email, password, and voice files from the system.

## Machine Learning Model 🧠
The voice verification algorithm is built using a Neural Network trained on the AudioMNIST dataset. It learns to compare two voice recordings and determine if they belong to the same speaker.

### 1. Data Preparation & Pair Generation
- The dataset consists of multiple folders, each representing a different speaker.
- The build_pairs() function creates:
  - Positive pairs (two recordings from the same speaker).
  - Negative pairs (recordings from different speakers).
```
def build_pairs(num_pairs=5, num_speakers=60, data_path='AudioMNIST/data'):
    speaker_folders = [os.path.join(data_path, folder) for folder in os.listdir(data_path) if os.path.isdir(os.path.join(data_path, folder))][:num_speakers]

    positive_pairs = []
    negative_pairs = []

    for this_speaker_folder in speaker_folders:
        files1 = [os.path.join(this_speaker_folder, f) for f in os.listdir(this_speaker_folder)]
        
        positive_pairs += random.sample(list(combinations(files1, 2)), num_pairs * (num_speakers - 1))
        
        for other_speaker_folder in set(speaker_folders) - {this_speaker_folder}:
            files2 = [os.path.join(other_speaker_folder, f) for f in os.listdir(other_speaker_folder)]
            negative_pairs += random.sample([(f1, f2) for f1 in files1 for f2 in files2], num_pairs)

    return [(pair, 1) for pair in positive_pairs] + [(pair, 0) for pair in negative_pairs]
```

### 2. Feature Extraction (MFCCs)
Each audio file is processed into Mel-Frequency Cepstral Coefficients (MFCCs), which capture important voice characteristics.
```
def extract_mfcc(file_path, n_mfcc=39):
    y, sr = librosa.load(file_path, sr=None)
    mfccs = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=n_mfcc)
    mfccs = np.mean(mfccs.T, axis=0)  # Taking mean across time
    return mfccs
```

### 3. Training the Neural Network
- The model takes the absolute difference between two feature vectors.
- It consists of three dense layers with ReLU activation.
- It predicts 1 (same speaker) or 0 (different speakers).
```
nn = tf.keras.Sequential([
    layers.Dense(64, activation="relu", input_shape=(39,)),
    layers.Dense(32, activation="relu"),
    layers.Dense(1, activation="sigmoid")
])
nn.compile(optimizer='adam', loss="binary_crossentropy", metrics=["accuracy"])
```
- The model is trained for 100 epochs using labeled data.
```
nn.fit(tf.abs(x1_train - x2_train), y_train, epochs=100)
nn.save('model.keras')
```

### 4. Model Evaluation
After training, the model is tested on unseen data to check its accuracy.
```
results = nn.evaluate(tf.abs(x1_test - x2_test), y_test)
print("test loss, test acc:", results)
```
## Tech Stack
### Backend (Voice Authentication & API)
Python
- FastAPI – API for processing and validating audio
- TensorFlow/Keras – Machine Learning for speaker verification
- Librosa – Audio feature extraction
- FFmpeg – Audio format conversion
- Uvicorn - An ASGI server that acts as a bridge between the FastAPI application (main.py) and the rest of the project. In case you want to run the project, you need to open Uvicorn server by running this command in the terminal: ``` uvicorn main:app ```
### Backend (Login & Registration System)
PHP – Handles user accounts & database interactions<br>
MySQL – Stores user credentials
### Frontend (Login & Registration System)
HTML, CSS, JavaScript – UI for authentication

