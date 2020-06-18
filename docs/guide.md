# General workflow {#general-workflow}


## Browsing {#browsing}

First, open the SoundEFForTS website at:

[http://soundefforts.uni-goettingen.de/](http://soundefforts.uni-goettingen.de/)

and login using your credentials written in the Working agreement. You get to the collections by clicking the tab “Sounds” at the top:

![alt_text](images/image1.png "image_tooltip")


Then you have access to the individual files of a collection. There is no particular order to process the recordings. Open a recording first by clicking on its spectrogram:

![alt_text](images/image2.png "image_tooltip")

It will take some time to load for 20min recordings as it is a big audio file (50-100 MB).

After that, the analysis window appears, from which you can visualize, playback, and annotate recordings.


## Listening {#listening}

Before you start processing the recording, you must listen to the entire recording in reading mode. To do that, click that button:



![alt_text](images/image3.png "image_tooltip")


And click the playback button:


![alt_text](images/image4.png "image_tooltip")


The window will automatically scroll until the end of the recording. You may take notes while listening to the recording.


## Navigation {#navigation}

To listen a specific part of the file, select it first by drawing a box, then press play.

Every second you listen to a recording is logged, so that we have an idea of the sampling intensity for each recording.

To visualize bird calls in detail, you will need to zoom in on them first. For that, select it in the spectrogram (1). If you do not want to hear the frequencies outside those you selected (higher and lower frequencies), uncheck the “filter” box (2); it is checked by default so that what you see is what you hear. Then zoom into it (3).

To come back to the overview, just click on the overview spectrogram or the home icon (4).


![alt_text](images/image5.png "image_tooltip")



### Tips for improving visibility {#tips-for-improving-visibility}

By default, Pumilio displays the _left channel_ spectrogram. In some instances, you might hear a bird while you cannot see it in the spectrogram. If that happens, make sure to check the other half of the spectrogram (the right channel), by changing the audio channel:


![alt_text](images/image6.png "image_tooltip")


## Annotation {#annotation}


### Selection {#selection}

Once you are positive about the identity of your chosen bird call, you should select the call from the beginning to the end like in this example:

![alt_text](images/image7.png "image_tooltip")

Only select the frequencies that contain the call (from ~1200 to ~2200 Hz in this example). Here, there is one bird which is calling out 2 times (1,2). You have to select the whole bird call sequence until the bird stops calling.

A close-up of the first call is here:

![alt_text](images/image8.png "image_tooltip")


Interestingly, after the main call, you can see a second call that is difficult to hear: it is the response of a distant conspecific bird, which should be annotated separately because it is not the same bird.

If you have toggled “show tags”, it may be difficult to annotate calls which overlap with other calls, so hide the tags first.

![alt_text](images/image9.png "image_tooltip")


### Tagging {#tagging}

Once you have identified the bird and selected its call, you can click the “add tag” annotation button.

![alt_text](images/image10.png "image_tooltip")


In a pop-up window, you can start typing the English or latin name of the bird and choose it from the list.

![alt_text](images/image11.png "image_tooltip")


Note that in case you only know the genus or maybe the family, these names are in the list as well and can be selected. You cannot input values that are not in the list. If you want to annotate monkey calls and insect buzzes, you can do so, there are entries in the list.

If the call is entirely unknown, select “0 - unknown”. There is a checkbox called “uncertain” if you are unsure of the ID.

To check your guesses, you can click the Google link which will take you to a Google Images search page. You can also click the Xeno-Canto link to compare the call with reference calls by other recordists.


### Additional call data {#additional-call-data}

You should fill out the following information for each call.

![alt_text](images/image12.png "image_tooltip")


**Number of individuals**: Usually, you will have only one bird, but if you hear a group of birds calling at the same time from the same place, you should write how many birds you hear. If you are unsure, write the minimum number of individuals.

**Call Type**: For birds there are calls, songs, and non-vocal sounds. For bats, there are feeding, searching and social calls.

**Reference call**: If you find a particularly good bird call, it is a reference call. We will upload these calls to Xeno-Canto and mention the names of the identifiers as acknowledgements.

Now you can confirm with “Save Changes”. The annotation will be stored.

You can also cancel tagging and close the window.


### Call distance {#call-distance}

Call distance must be filled in after hearing an unfiltered version of the call. Otherwise, filtered audio sounds too unnatural so that the distance estimation could be biased. Distance estimation works only by clicking this special button after the tag is created:


![alt_text](images/image13.png "image_tooltip")


This zooms in to the first 30 s of the call and plays it back but makes the entire frequency range audible to avoid bias in the estimation. In parallel, you should listen to the corresponding sound transmission recording (usually suffixed with "transmission" in the name) to gauge your own hearing to the location's and microphones' specific sound transmission according to frequency.

At the end of the 30 seconds or when you pause or stop, you will be prompted to fill in your distance estimate in meters, do so without binning - even if you are unsure.

If the animal is constantly moving, write the closest distance you heard the bird at.

**Bat recordings** do not need that information as it is impossible to intuitively estimate their distance and they are constantle moving, so only for bats you should check the “distance not estimable” checkbox.


# 


# Additional instructions for bats {#additional-instructions-for-bats}


# Bat call learning {#bat-call-learning}

You can be given viewing privilages on collections that were already analysed by someone else to learn to distinguish bat tags from noise.


# Bat call tagging {#bat-call-tagging}


## Learning to tag {#learning-to-tag}

Log in to SoundEFForTS:

[http://soundefforts.uni-goettingen.de/](http://soundefforts.uni-goettingen.de/) 

Take a look at the recordings inside the collection “Exclosures (ultrasound) **1**”. Check some tags to get a feeling what the different bat call types look like in Jambi. Once you are confident enough with Soundefforts, go on to the next part.


## Scanning recordings {#scanning-recordings}

Open the collection that you have to process.

Your task is to find bat calls inside the recordings. You search them visually, but you can hear their calls by lowering the playback speed (to 0.1 or lower). But you must check all channels if there is more than one.

To scan your file, draw a box encompassing all frequencies and a part of the time axis, starting at the beginning of the recording:

![alt_text](images/image14.png "image_tooltip")

The box should be a maximum of 10 seconds large, and you can see it in the time range indicator:

![alt_text](images/image15.png "image_tooltip")


Now use the arrow buttons to scan the file from the beginning to the end while checking it for bat calls:

![alt_text](images/image16.png "image_tooltip")


When you are finished, **check the other channel** (if there is another one) for missed calls by switching channels:

![alt_text](images/image17.png "image_tooltip")



## Tagging calls {#tagging-calls}

When you found a bat call, you can draw a selection box and use the zoom (

![alt_text](images/image18.png "image_tooltip")
) to look at it more in detail.

Once you are certain it is a bat call, draw a selection box to tag the bat call, including the whole call from the beginning to the end like this:


![alt_text](images/image19.png "image_tooltip")


Press the tag button:


![alt_text](images/image20.png "image_tooltip")


Give different tag names (A, B, C, etc. based on different call attributes such as frequency range, call shape and duration) for different call types, or assign to species or higher taxa if you can. 

![alt_text](images/image21.png "image_tooltip")


Distinguish between searching, feeding and social calls:

![alt_text](images/image22.png "image_tooltip")


No need to estimate distances with bat calls, it is not possible yet. Just check the “distance not estimable” box.

For each tag, please write whether the bat call was detected on both channels or only one (which one), consistently, with the same text, into the comment field.

If there is more than 10 s silence between calls, separate tags should be created.


# Handling insect noise {#handling-insect-noise}

I need to know how often the sampling was impeded by insect noise. When you see so much noise in the ultrasound range that you would not be able to detect any bats even if they were there, you need to mark these. Tag all insect noise as "2 - insect" in soundefforts.

I compensated by uploading correspondingly long recordings to have an equal sampling duration for all plots.
