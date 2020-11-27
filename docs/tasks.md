# Tasks 
## Acoustic indices
### Theoretical background
Acoustic indices are mathematically computed, numerical values that synthesize different aspects of a sound recording. Generally, these indices summarise the variability or information content of sound into one single number. They can be computed for any audio and usually relate to measures of biodiversity (Buxton et al., 2018); they are practical but do not give detailed info about species identities.
### Technical implementation
1. Translate the formulae for computing the three most common and performant acoustic indices (short literature search) into python scripts
2. After local testing with existing recordings and verification against independent results provided by R packages such as seewave, integrate the scripts into BioSounds to compute acoustic indices of the presently-generated audio (selected with drop-down menu) with the press of a button. This might require generating a new WAV file.
3. Store the results (recording, chosen index, date and user, time and frequency coordinates) in a table accessible to the user for download from his admin interface.

## Soundscape/timeline collection view
### Theoretical background
We need to order multiple recordings of the same site on a common time axis, with sites in different rows, just like implemented in Ecosounds. This feature, which would be a proper soundscape visualization tool, will allow to see the true extent of the acoustic sampling for single sites, and to compare the sampling times across sites. This kind of visualization is usually the one associated with the concept of soundscapes. It requires the input of site and time information for each recording.
### Technical implementation
1. implement the soundscape view as an overview of the recordings in a given collection, with a new button next to the list and gallery views.
2. Group recordings in rows according to unique sites, name rows by site
3. Arrange recordings along a time axis, with info extracted from the files’ time, date, and duration
4. Since the recordings might be very narrow compared to the overall displayed time interval, we need a zooming function (by marking a range on the time axis) that operates across all sites. Spectrograms have to be re-computed after zooming, but if this takes too long, one can also pre-generate spectrograms at pre-definet time zooming increments.
5. Clicking on any particular recording should open it in the corresponding spectrogram player

## Automated analysis of sound transmission sequences
### Theoretical background
Depending on their setup, sound recorders have different detection ranges; to measure these, we use sound transmission sequences (Darras et al., 2016). We use the resulting sampling space (area) to standardise our biodiversity data. This is especially important for bats, whose detection distances are constantly chaging when in flight, and impossible to estimate intuitively such as can be done for birds (Darras et al., 2018). However, extracting audio signal measurements from sound transmission sequences is tedious, repetitive work. We need to develop a function to automatically extract the amplitude (in dB) of test tones and ambient sound at particular frequencies and distances.
### Technical implementation
R scripts have already been written in Darras et al. (2016) and can be used as a start for coding a python script. The function should
1. detect signal tones automatically in pure-user-defined frequency ranges based on a peaks detection function
2. Detect ambient sound in intervals between signal tones, excluding peaks (i.e., sounds other than the “background noise”)
3. Require user input to confirm the automated detections and prompt for distances (and direction)
4. Allow user input for those signal tones that cannot be automatically detected because they are too faint (too far)
5. measure the mean amplitude of the signal tones and ambient sound from the spectrogram
6. Output the result in a CSV file with recording ID, distance, direction, frequency, type of sound (signal/ambient), and amplitude
7. Graph the result with sound transmission profiles such as in (Darras et al., 2016)

# References
Buxton, R.T., McKenna, M.F., Clapp, M., Meyer, E., Stabenau, E., Angeloni, L.M., Crooks, K., Wittemyer, G., 2018. Efficacy of extracting indices from large-scale acoustic recordings to monitor biodiversity. Conserv. Biol. 32, 1174–1184. https://doi.org/10.1111/cobi.13119

Darras, K., Furnas, B., Fitriawan, I., Mulyani, Y., Tscharntke, T., 2018. Estimating bird detection distances in sound recordings for standardizing detection ranges and distance sampling. Methods Ecol. Evol. 9, 1928–1938. https://doi.org/10.1111/2041-210X.13031

Darras, K., Pütz, P., Fahrurrozi, Rembold, K., Tscharntke, T., 2016. Measuring sound detection spaces for acoustic animal sampling and monitoring. Biol. Conserv. 201, 29–37. https://doi.org/10.1016/j.biocon.2016.06.021
