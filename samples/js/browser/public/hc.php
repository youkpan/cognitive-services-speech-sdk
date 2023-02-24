<!DOCTYPE html>
<html lang="en">
<head>
  <title>Microsoft Cognitive Services Speech SDK JavaScript Sample for Speech Synthesis</title>
  <meta charset="utf-8" />
  <script src="https://ajax.aspnetcdn.com/ajax/jquery/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', 'Helvetica Neue', sans-serif;
      font-size: 14px;
    }

    table, th, td {
      border: 1px solid #f1f1f1;
      border-collapse: collapse;
    }

    th, td {
      padding: 10px;
    }

    textarea {
      font-family: Arial,sans-serif;
    }

    .mode {
      font-size: 18px;
    }

    .highlight{
      background-color: yellow;
    }

    input:not(disabled) {
      font-weight: bold;
      color: black;
    }

    button {
      padding: 4px 8px;
      background: #0078d4;
      color: #ffffff;
    }

    button:disabled {
      padding: 4px 8px;
      background: #ccc;
      color: #666;
    }

    input[type=radio] {
      position: relative;
      z-index: 1;
    }

    input[type=radio] + label {
      padding: 8px 4px 8px 30px;
      margin-left: -30px;
    }

    input[type=radio]:checked + label {
      background: #0078d4;
      color: #ffffff;
    }
  </style>
</head>
<body>
  <div id="warning">
    <h1 style="font-weight:500;">Speech Speech SDK not found
      (microsoft.cognitiveservices.speech.sdk.bundle.js missing).</h1>
  </div>

  <div id="content" style="display:none">
    <table>
      <tr>
        <td></td>
        <td><h1 style="font-weight:500;">Microsoft Cognitive Services Speech SDK JavaScript Sample for Speech Synthesis</h1></td>
      </tr>
      <tr>
        <td align="right">
          <label for="subscriptionKey">
            <a href="https://docs.microsoft.com/azure/cognitive-services/speech-service/get-started"
               rel="noreferrer noopener"
               target="_blank">Subscription Key</a>
          </label>
        </td>
        <td><input id="subscriptionKey" type="text" size="40" placeholder="YourSubscriptionKey"></td>
      </tr>
      <tr>
        <td align="right"><label for="regionOptions">Region</label></td>
        <td>
<!--          see https://aka.ms/csspeech/region for more details-->
          <select id="regionOptions">
            <option value="westus" selected="selected">West US</option>
            <option value="westus2">West US2</option>
            <option value="eastus">East US</option>
            <option value="eastus2">East US2</option>
            <option value="centralus">Central US</option>
            <option value="northcentralus">North Central US</option>
            <option value="southcentralus">South Central US</option>
            <option value="westcentralus">West Central US</option>
            <option value="canadacentral">Canada Central</option>
            <option value="brazilsouth">Brazil South</option>
            <option value="eastasia">East Asia</option>
            <option value="southeastasia">South East Asia</option>
            <option value="australiaeast">Australia East</option>
            <option value="centralindia">Central India</option>
            <option value="japaneast">Japan East</option>
            <option value="japanwest">Japan West</option>
            <option value="koreacentral">Korea Central</option>
            <option value="northeurope">North Europe</option>
            <option value="westeurope">West Europe</option>
            <option value="francecentral">France Central</option>
            <option value="switzerlandnorth">Switzerland North</option>
            <option value="uksouth">UK South</option>
            <option value="chinaeast2">China East2 (azure.cn)</option>
            <option value="chinanorth2">China North2 (azure.cn)</option>
          </select>
        </td>
      </tr>
      <tr>
        <td align="right"><label for="voiceOptions">Voice</label></td>
        <td>
          <button id="updateVoiceListButton">Update Voice List</button>
          <select id="voiceOptions" disabled>
            <option>Please update voice list first.</option>
          </select>
        </td>
      </tr>
      <tr>
        <td align="right"><label for="formatOptions">Format</label></td>
        <td>
          <select id="formatOptions">
            <option>Waiting for SDK loading.</option>
          </select>
          (riff pcm, mp3, ogg and webm formats are supported for playback.)
        </td>
      </tr>
      <tr>
        <td align="right"><label for="isSSML">Is SSML</label><br></td>
        <td>
          <input type="checkbox" id="isSSML" name="isSSML" value="ssml">
        </td>
      </tr>
      <tr>
        <td align="right"><label for="synthesisText">Text</label></td>
        <td>
          <textarea id="synthesisText" style="display: inline-block;width:500px;height:100px"
                 placeholder="Input text or ssml for synthesis."></textarea>
        </td>
      </tr>
      <tr>
        <td></td>
        <td>
          <button id="startSynthesisAsyncButton">Start synthesis</button>
          <button id="pauseButton">Pause</button>
          <button id="resumeButton">Resume</button>
          <button id="downloadButton">Download</button>
        </td>
      </tr>
      <tr>
        <td align="right" valign="top"><label for="resultsDiv">Results</label></td>
        <td><textarea id="resultsDiv" readonly style="display: inline-block;width:500px;height:50px"></textarea></td>
      </tr>
      <tr>
        <td align="right" valign="top"><label for="talkingHeadDiv">
          <a href="https://docs.microsoft.com/azure/cognitive-services/speech-service/how-to-speech-synthesis-viseme?pivots=programming-language-javascript"
            rel="noreferrer noopener"
            target="_blank">Talking Head</a></label></td>
        <td><div id="talkingHeadDiv" style="display: inline-block;width:800px;"></div></td>
      </tr>
      <tr>
        <td align="right" valign="top"><label for="eventsDiv">Events</label></td>
        <td><textarea id="eventsDiv" readonly style="display: inline-block;width:500px;height:200px"></textarea></td>
      </tr>
      <tr>
        <td align="right" valign="top"><label for="highlightDiv">Highlight</label></td>
        <td><div id="highlightDiv" style="display: inline-block;width:800px;"></div></td>
      </tr>
    </table>
  </div>

  <!-- Speech SDK reference sdk. -->
  <script src="https://aka.ms/csspeech/jsbrowserpackageraw"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

  <!-- Speech Speech SDK Authorization token -->
  <script>
      // Note: Replace the URL with a valid endpoint to retrieve
      //       authorization tokens for your subscription.

      // An authorization token is a more secure method to authenticate for a browser deployment as
      // it allows the subscription keys to be kept secure on a server and a 10 minute use token to be
      // handed out to clients from an endpoint that can be protected from unauthorized access.
      var authorizationEndpoint = "token.php";

      async function RequestAuthorizationToken() {
        if (authorizationEndpoint) {
          try {
            const res = await axios.get(authorizationEndpoint);
            const token = res.data.token;
            const region = res.data.region;
            regionOptions.value = region;
            authorizationToken = token;

            console.log('Token fetched from back-end: ' + token);
          } catch (err) {
              console.log(err);
          }
        }
      }
  </script>

  <!-- Speech SDK USAGE -->
  <script>
    // On document load resolve the Speech SDK dependency
    function Initialize(onComplete) {
      if (!!window.SpeechSDK) {
        document.getElementById('content').style.display = 'block';
        document.getElementById('warning').style.display = 'none';
        onComplete(window.SpeechSDK);
      }
    }
  </script>

  <!-- Browser Hooks -->
  <script>
    // status fields and start button in UI
    var resultsDiv,
        eventsDiv,
        talkingHeadDiv,
        highlightDiv;
    var startSynthesisAsyncButton, pauseButton, resumeButton, downloadButton;
    var updateVoiceListButton;

    // subscription key and region for speech services.
    var subscriptionKey, regionOptions;
    var authorizationToken;
    var voiceOptions, isSsml;
    var SpeechSDK;
    var synthesisText;
    var synthesizer;
    var player;
    var wordBoundaryList = [];

    function getExtensionFromFormat(format) {
      format = format.toLowerCase();
      if (format.includes('mp3')) {
        return 'mp3';
      } else if (format.includes('ogg')) {
        return 'ogg';
      } else if (format.includes('webm')) {
        return 'webm';
      } else if (format.includes('ogg')) {
        return 'ogg';
      } else if (format.includes('silk')) {
        return 'silk';
      } else if (format.includes('riff')) {
        return 'wav';
      } else {
        return 'pcm';
      }
    }

    document.addEventListener("DOMContentLoaded", function () {
      startSynthesisAsyncButton = document.getElementById("startSynthesisAsyncButton");
      updateVoiceListButton = document.getElementById("updateVoiceListButton");
      pauseButton = document.getElementById("pauseButton");
      resumeButton = document.getElementById("resumeButton");
      downloadButton = document.getElementById("downloadButton");
      subscriptionKey = document.getElementById("subscriptionKey");
      regionOptions = document.getElementById("regionOptions");
      resultsDiv = document.getElementById("resultsDiv");
      eventsDiv = document.getElementById("eventsDiv");
      voiceOptions = document.getElementById("voiceOptions");
      isSsml = document.getElementById("isSSML");
      talkingHeadDiv = document.getElementById("talkingHeadDiv");
      highlightDiv = document.getElementById("highlightDiv");

      setInterval(function () {
        if (player !== undefined) {
          const currentTime = player.currentTime;
          var wordBoundary;
          for (const e of wordBoundaryList) {
            if (currentTime * 1000 > e.audioOffset / 10000) {
              wordBoundary = e;
            } else {
              break;
            }
          }
          if (wordBoundary !== undefined) {
            highlightDiv.innerHTML = synthesisText.value.substr(0, wordBoundary.textOffset) +
                    "<span class='highlight'>" + wordBoundary.text + "</span>" +
                    synthesisText.value.substr(wordBoundary.textOffset + wordBoundary.wordLength);
          } else {
            highlightDiv.innerHTML = synthesisText.value;
          }
        }
      }, 50);

      updateVoiceListButton.addEventListener("click", function () {
        var request = new XMLHttpRequest();
        request.open('GET',
                'https://' + regionOptions.value + ".tts.speech." +
                (regionOptions.value.startsWith("china") ? "azure.cn" : "microsoft.com") +
                        "/cognitiveservices/voices/list", true);
        if (authorizationToken) {
          request.setRequestHeader("Authorization", "Bearer " + authorizationToken);
        } else {
          if (subscriptionKey.value === "" || subscriptionKey.value === "subscription") {
            alert("Please enter your Microsoft Cognitive Services Speech subscription key!");
            return;
          }
          request.setRequestHeader("Ocp-Apim-Subscription-Key", subscriptionKey.value);
        }

        request.onload = function() {
          if (request.status >= 200 && request.status < 400) {
            const response = this.response;
            const defaultVoice = "JennyNeural";
            let selectId;
            const data = JSON.parse(response);
            voiceOptions.innerHTML = "";
            data.forEach((voice, index) => {
              voiceOptions.innerHTML += "<option value=\"" + voice.Name + "\">" + voice.Name + "</option>";
              if (voice.Name.indexOf(defaultVoice) > 0) {
                selectId = index;
              }
            });
            voiceOptions.selectedIndex = selectId;
            voiceOptions.disabled = false;
          } else {
            window.console.log(this);
            eventsDiv.innerHTML += "cannot get voice list, code: " + this.status + " detail: " + this.statusText + "\r\n";
          }
        };

        request.send()
      });

      pauseButton.addEventListener("click", function () {
        player.pause();
        pauseButton.disabled = true;
        resumeButton.disabled = false;
      });

      resumeButton.addEventListener("click", function () {
        player.resume();
        pauseButton.disabled = false;
        resumeButton.disabled = true;
      });

      startSynthesisAsyncButton.addEventListener("click", function () {
        resultsDiv.innerHTML = "";
        eventsDiv.innerHTML = "";
        wordBoundaryList = [];
        synthesisText = document.getElementById("synthesisText");

        // if we got an authorization token, use the token. Otherwise use the provided subscription key
        var speechConfig;
        if (authorizationToken) {
          speechConfig = SpeechSDK.SpeechConfig.fromAuthorizationToken(authorizationToken, regionOptions.value);
        } else {
          if (subscriptionKey.value === "" || subscriptionKey.value === "subscription") {
            alert("Please enter your Microsoft Cognitive Services Speech subscription key!");
            return;
          }
          speechConfig = SpeechSDK.SpeechConfig.fromSubscription(subscriptionKey.value, regionOptions.value);
        }

        speechConfig.speechSynthesisVoiceName = voiceOptions.value;
        speechConfig.speechSynthesisOutputFormat = formatOptions.value;

        player = new SpeechSDK.SpeakerAudioDestination();
        player.onAudioStart = function(_) {
          window.console.log("playback started");
          setTimeout(function(){ $("svg path :first-child").each( function(i) {this.beginElement();}); }, 0.5);
        }
        player.onAudioEnd = function (_) {
          window.console.log("playback finished");
          eventsDiv.innerHTML += "playback finished" + "\r\n";
          startSynthesisAsyncButton.disabled = false;
          downloadButton.disabled = false;
          pauseButton.disabled = true;
          resumeButton.disabled = true;
          wordBoundaryList = [];
        };

        var audioConfig  = SpeechSDK.AudioConfig.fromSpeakerOutput(player);

        synthesizer = new SpeechSDK.SpeechSynthesizer(speechConfig, audioConfig);

        // The event synthesizing signals that a synthesized audio chunk is received.
        // You will receive one or more synthesizing events as a speech phrase is synthesized.
        // You can use this callback to streaming receive the synthesized audio.
        synthesizer.synthesizing = function (s, e) {
          window.console.log(e);
          eventsDiv.innerHTML += "(synthesizing) Reason: " + SpeechSDK.ResultReason[e.result.reason] +
                  "Audio chunk length: " + e.result.audioData.byteLength + "\r\n";
        };

        // The synthesis started event signals that the synthesis is started.
        synthesizer.synthesisStarted = function (s, e) {
          window.console.log(e);
          eventsDiv.innerHTML += "(synthesis started)" + "\r\n";
          pauseButton.disabled = false;
        };

        // The event synthesis completed signals that the synthesis is completed.
        synthesizer.synthesisCompleted = function (s, e) {
          console.log(e);
          eventsDiv.innerHTML += "(synthesized)  Reason: " + SpeechSDK.ResultReason[e.result.reason] +
                  " Audio length: " + e.result.audioData.byteLength + "\r\n";
        };

        // The event signals that the service has stopped processing speech.
        // This can happen when an error is encountered.
        synthesizer.SynthesisCanceled = function (s, e) {
          const cancellationDetails = SpeechSDK.CancellationDetails.fromResult(e.result);
          let str = "(cancel) Reason: " + SpeechSDK.CancellationReason[cancellationDetails.reason];
          if (cancellationDetails.reason === SpeechSDK.CancellationReason.Error) {
            str += ": " + e.result.errorDetails;
          }
          window.console.log(e);
          eventsDiv.innerHTML += str + "\r\n";
          startSynthesisAsyncButton.disabled = false;
          downloadButton.disabled = false;
          pauseButton.disabled = true;
          resumeButton.disabled = true;
        };

        // This event signals that word boundary is received. This indicates the audio boundary of each word.
        // The unit of e.audioOffset is tick (1 tick = 100 nanoseconds), divide by 10,000 to convert to milliseconds.
        synthesizer.wordBoundary = function (s, e) {
          window.console.log(e);
          eventsDiv.innerHTML += "(WordBoundary), Text: " + e.text + ", Audio offset: " + e.audioOffset / 10000 + "ms." + "\r\n";
          wordBoundaryList.push(e);
        };

        synthesizer.visemeReceived = function (s, e) {
          window.console.log(e);
          eventsDiv.innerHTML += "(Viseme), Audio offset: " + e.audioOffset / 10000 + "ms. Viseme ID: " + e.visemeId + '\n';
          talkingHeadDiv.innerHTML = e.animation.replaceAll("begin=\"0.5s\"", "begin=\"indefinite\"");
          $("svg").width('500px').height('500px');
        }

        synthesizer.bookmarkReached = function (s, e) {
          window.console.log(e);
          eventsDiv.innerHTML +=  "(Bookmark reached), Audio offset: " + e.audioOffset / 10000 + "ms. Bookmark text: " + e.text + '\n';
        }

        const complete_cb = function (result) {
          if (result.reason === SpeechSDK.ResultReason.SynthesizingAudioCompleted) {
            resultsDiv.innerHTML += "synthesis finished";
          } else if (result.reason === SpeechSDK.ResultReason.Canceled) {
            resultsDiv.innerHTML += "synthesis failed. Error detail: " + result.errorDetails;
          }
          window.console.log(result);
          synthesizer.close();
          synthesizer = undefined;
        };
        const err_cb = function (err) {
          startSynthesisAsyncButton.disabled = false;
          downloadButton.disabled = false;
          phraseDiv.innerHTML += err;
          window.console.log(err);
          synthesizer.close();
          synthesizer = undefined;
        };

        if (!synthesisText.value) {
          alert("Please enter synthesis content.");
          return;
        }

        startSynthesisAsyncButton.disabled = true;
        downloadButton.disabled = true;

        if (isSsml.checked) {
          synthesizer.speakSsmlAsync(synthesisText.value,
                  complete_cb,
                  err_cb);
        } else {
          synthesizer.speakTextAsync(synthesisText.value,
                  complete_cb,
                  err_cb);
        }
      });

      downloadButton.addEventListener("click", function () {
        resultsDiv.innerHTML = "";
        eventsDiv.innerHTML = "";
        synthesisText = document.getElementById("synthesisText");

        var speechConfig;

        // if we got an authorization token, use the token. Otherwise use the provided subscription key
        if (authorizationToken) {
          speechConfig = SpeechSDK.SpeechConfig.fromAuthorizationToken(authorizationToken, regionOptions.value);
        } else {
          if (subscriptionKey.value === "" || subscriptionKey.value === "subscription") {
            alert("Please enter your Microsoft Cognitive Services Speech subscription key!");
            return;
          }
          speechConfig = SpeechSDK.SpeechConfig.fromSubscription(subscriptionKey.value, regionOptions.value);
        }

        speechConfig.speechSynthesisVoiceName = voiceOptions.value;
        speechConfig.speechSynthesisOutputFormat = formatOptions.value;

        synthesizer = new SpeechSDK.SpeechSynthesizer(speechConfig, null);

        synthesizer.SynthesisCanceled = function (s, e) {
          const cancellationDetails = SpeechSDK.CancellationDetails.fromResult(e.result);
          let str = "(cancel) Reason: " + SpeechSDK.CancellationReason[cancellationDetails.reason];
          if (cancellationDetails.reason === SpeechSDK.CancellationReason.Error) {
            str += ": " + e.result.errorDetails;
          }
          window.console.log(e);
          eventsDiv.innerHTML += str + "\r\n";
          resultsDiv.innerHTML = str;
          startSynthesisAsyncButton.disabled = false;
          downloadButton.disabled = false;
          pauseButton.disabled = true;
          resumeButton.disabled = true;
        };

        synthesizer.synthesisCompleted = function (s, e) {
            resultsDiv.innerHTML = "synthesis finished";
            synthesizer.close();
            a = document.createElement('a');
            url = window.URL.createObjectURL(new Blob([e.result.audioData]));
            a.href = url;
            a.download = 'synth.' + getExtensionFromFormat(formatOptions.options[formatOptions.selectedIndex].text);
            document.body.appendChild(a);
            a.click();
            setTimeout(function () {
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            }, 0);
            startSynthesisAsyncButton.disabled = false;
            downloadButton.disabled = false;
        };

        if (!synthesisText.value) {
          alert("Please enter synthesis content.");
        }

        startSynthesisAsyncButton.disabled = true;
        downloadButton.disabled = true;

        if (isSsml.checked) {
          synthesizer.speakSsmlAsync(synthesisText.value);
        } else {
          synthesizer.speakTextAsync(synthesisText.value);
        }
      });

      Initialize(async function (speechSdk) {
        SpeechSDK = speechSdk;
        startSynthesisAsyncButton.disabled = false;
        downloadButton.disabled = false;
        pauseButton.disabled = true;
        resumeButton.disabled = true;

        formatOptions.innerHTML = "";
        Object.keys(SpeechSDK.SpeechSynthesisOutputFormat).forEach(format => {
          if (isNaN(format) && !format.includes('Siren')) {
            formatOptions.innerHTML += "<option value=\"" + SpeechSDK.SpeechSynthesisOutputFormat[format] + "\">" + format + "</option>"
          }}
        );
        formatOptions.selectedIndex = SpeechSDK.SpeechSynthesisOutputFormat.Audio24Khz48KBitRateMonoMp3;

        // in case we have a function for getting an authorization token, call it.
        if (typeof RequestAuthorizationToken === "function") {
           await RequestAuthorizationToken();
        }
      });
    });
  </script>
</body>
</html>
