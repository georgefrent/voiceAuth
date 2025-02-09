from fastapi import FastAPI, File, UploadFile, HTTPException, Form
from fastapi.responses import JSONResponse
from fastapi.middleware.cors import CORSMiddleware
from pathlib import Path
import numpy as np
import librosa
import tensorflow as tf
from tensorflow.keras import layers
import os

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=['*'],
    allow_credentials=True,
    allow_methods=['*'],
    allow_headers=['*'],
)


UPLOAD_DIR = Path('uploaded_audio')
UPLOAD_DIR.mkdir(exist_ok=True)


nn = tf.keras.Sequential([
    layers.Dense(64, activation='relu', input_shape=(39,)),
    layers.Dense(32, activation='relu'),
    layers.Dense(1, activation='sigmoid')
])
nn.compile(optimizer='adam', loss='binary_crossentropy', metrics=['accuracy'])

nn.load_weights('./model.keras')

def extract_mfcc(file_path, n_mfcc=39):
    y, sr = librosa.load(file_path, sr=None)
    mfccs = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=n_mfcc)
    mfccs = np.mean(mfccs.T, axis=0)
    return mfccs


@app.post('/{user_name}/audio')
async def upload_audio(user_name, audio: UploadFile = File(...)):

    if not audio.content_type.startswith('audio/'):
        raise HTTPException(status_code=400, detail='Invalid file type. Only audio files are allowed.')
    
    user_upload_dir = UPLOAD_DIR.joinpath(user_name)
    user_upload_dir.mkdir(exist_ok=True)


    file_path = user_upload_dir / audio.filename
    with file_path.open('wb') as f:
        f.write(await audio.read())


    input_file = str(file_path)
    output_file = str(file_path).replace('webm', 'wav')

    print(input_file)
    print(output_file)

    os.system(f'ffmpeg.exe -i {input_file} {output_file}')
    os.remove(input_file)

    this_recording = np.array([extract_mfcc(output_file)])
    to_be_compared_to = np.array([extract_mfcc(user_upload_dir / 'Zero.wav')])

    prediction = nn.predict(tf.abs(this_recording - to_be_compared_to))
    probability_same = prediction[0][0]
    same = probability_same > 0.5

    return JSONResponse({
        'prob_is_same': str(probability_same),
        'is_same': str(same)
    })