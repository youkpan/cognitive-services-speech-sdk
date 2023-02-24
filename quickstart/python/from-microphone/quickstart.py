# Copyright (c) Microsoft. All rights reserved.
# Licensed under the MIT license. See LICENSE.md file in the project root for full license information.

# <code>
import azure.cognitiveservices.speech as speechsdk
language = ""#input("请输入你要对话的语言，比如中文 zh-CN 。空白输入是中文")
if language=="" :
    language = "zh-CN"
# Creates an instance of a speech config with specified subscription key and service region.
# Replace with your own subscription key and service region (e.g., "westus").
speech_key, service_region = "66fc9b64ff8546b9a6c23433f3f79c36", "eastasia"
speech_config = speechsdk.SpeechConfig(subscription=speech_key, region=service_region)

# Creates a recognizer with the given settings
speech_config.speech_recognition_language = language
speech_recognizer = speechsdk.SpeechRecognizer(speech_config=speech_config)
'''
zh-CN-XiaochenNeural4、5、6（女）
zh-CN-XiaohanNeural2、4、5、6（女）
zh-CN-XiaomengNeural1、2、4、5、6（女）
zh-CN-XiaomoNeural2、3、4、5、6（女）
zh-CN-XiaoqiuNeural4、5、6（女）
zh-CN-XiaoruiNeural2、4、5、6（女）
zh-CN-XiaoshuangNeural2、4、5、6、8（女）
zh-CN-XiaoxiaoNeural2、4、5、6（女）
zh-CN-XiaoxuanNeural2、3、4、5、6（女）
zh-CN-XiaoyanNeural4、5、6（女）
zh-CN-XiaoyiNeural1、2、4、5、6（女）
zh-CN-XiaoyouNeural4、5、6、8（女）
zh-CN-XiaozhenNeural1、2、4、5、6（女）
zh-CN-YunfengNeural1、2、4、5、6（男）
zh-CN-YunhaoNeural1、2、4、5、6（男）
zh-CN-YunjianNeural1、2、4、5、6（男）
zh-CN-YunxiaNeural1、2、4、5、6（男）
zh-CN-YunxiNeural2、3、4、5、6（男）
zh-CN-YunyangNeural2、4、5、6（男）
zh-CN-YunyeNeural2、3、4、5、6（男）
zh-CN-YunzeNeural1、2、3、4、5、6（男）
'''
# Set the voice name, refer to https://aka.ms/speech/voices/neural for full list.
speech_config.speech_synthesis_voice_name = "zh-CN-XiaomengNeural"
speech_config.speech_recognition_language = language
# Creates a speech synthesizer using the default speaker as audio output.
speech_synthesizer = speechsdk.SpeechSynthesizer(speech_config=speech_config)

print("说点什么~")
import requests
from urllib.parse import urlencode

prompt0 = "You are a super artificial intelligence AI assistant created by 展映科技 based on OpenAI chatGPT." +\
	"You are very friendly,intelligent,helpful,and creative. Your answers should be as short,intuitive,logical,positive and interesting." +\
	" But you don't have the internet and don't know the time.The following is a conversation between a person and you, reply mostly in Chinese.\n"
prompt = ""
# Starts speech recognition, and returns after a single utterance is recognized. The end of a
# single utterance is determined by listening for silence at the end or until a maximum of 15
# seconds of audio is processed.  The task returns the recognition text as result. 
# Note: Since recognize_once() returns only a single utterance, it is suitable only for single
# shot recognition like command or query. 
# For long-running multi-utterance recognition, use start_continuous_recognition() instead.
while True:
    print("你: ")
    result = speech_recognizer.recognize_once()

    # Checks result.
    if result.reason == speechsdk.ResultReason.RecognizedSpeech:
        
        print("{}".format(result.text))
        # Receives a text from console input.
        #print("Type some text that you want to speak...")
        #text = input()
        if result.text.find("重新开始")!=-1:
            prompt = ""
            continue
        prompt = prompt + "\nQ:" + result.text +"\nA:"
        openid = "test"
        userid = "test"
        params2 = { 'q': prompt0 + prompt }
        url = "https://v.stylee.top/chatapi?openid="+openid+"&userid="+userid+"&process_type=simple&q="+urlencode(params2)
        data = requests.get(url=url,timeout=(10, 600))
        #data = resp.json() # Check the JSON Response Content documentation below
        chatbot = data.text
        print("\n智慧助手:"+chatbot)
        print()
        prompt = prompt + "\nA:" + chatbot
        # Synthesizes the received text to speech.
        # The synthesized speech is expected to be heard on the speaker with this line executed.
        result = speech_synthesizer.speak_text_async(chatbot).get()
    else:
        break

    if result.reason == speechsdk.ResultReason.NoMatch:
        print("No speech could be recognized: {}".format(result.no_match_details))
    elif result.reason == speechsdk.ResultReason.Canceled:
        cancellation_details = result.cancellation_details
        print("Speech Recognition canceled: {}".format(cancellation_details.reason))
        if cancellation_details.reason == speechsdk.CancellationReason.Error:
            print("Error details: {}".format(cancellation_details.error_details))
    # </code>

