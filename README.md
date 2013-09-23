Taxonomy as impediment: synonymy and its impact on the Global Biodiversity Information Facility's database
============================================

Roderic D. M. Page
Institute of Biodiversity, Animal Health and Comparative Medicine
College of Medical, Veterinary and Life Sciences
Graham Kerr Building
University of Glasgow
Glasgow G12 8QQ, UK

# Abstract

The GBIF backbone taxonomy contains numerous duplicate taxa due to unrecognised synonyms being included multiple times. These synonyms may be linked to distinct occurrence data, such that a search on one name is unlikely to retrieve all occurrence data for the corresponding taxon. The potential extent of hidden synonymy is quantified in several taxonomic groups, and visualised using cluster maps. Anecdotally taxa most prone to unrecognised synonyms are those that are supplied by multiple databases, or where there is a tradition of using subgenera.

# Introduction

Taxonomic names are the default global identifier for biodiversity information [Sarkar 2007],[Patterson et al. 2010]. However, they are poorly equipped for this role [Kennedy et al. 2005]. Ideally an identifier would be stable, the entity it identifies would itself be stable (or, at least, its boundaries be uncontested), and there would be a single way to display the identifier. All of these desirable attributes are missing from taxonomic names. Names carry semantic information, or at least biologists treat the names as being meaningful. For example, inclusion of chimps and gorillas in the genus *Homo* [Wildman et al. 2003] carries with it legal implications regarding our treatment of our closest living relatives [Taylor 2001]. The genus part of a species name is expected to be a reliable indicator of that species' relationships, such that species in the same genus are expected to be more closely related to each other than to species in other genera. When it becomes apparent that this is not the case, taxonomists will typically redistribute the species across genera until a more satisfactory arrangement is arrived at. The resulting name changes come at the potential cost of disrupting communication, sometimes to an extent that the change is resisted. The fruit fly *Drosophila melanogaster* is a recent case in point [Dalton 2010].

Even the basic requirement that there be a single way of representing an identifier is violated. A taxonomic name may be written with or without the taxon authorship (the name of the taxonomist(s) who coined the name) and, depending on rank, there may be more than one way to write the same name. For example, if we follow [Prouty et al. 1983] and place the white-faced gibbon in the subgenus *Bunopithecus* (Matthew and Granger 1923) we could write the taxonomic name as either *Hylobates hoolock* or *Hylobates (Bunopithecus) hillock*.

This multiplicity of names complicates the task of aggregating biodiversity data at a global scale. If different data sources use different names for the same taxon and the aggregator isn't aware that the names are synonyms, the aggregator will not merge the different data sets together correctly. The Global Biodiversity Information Facility (GBIF) is the largest global aggregator of fundamental biodiversity data, focussing on observations of organisms that are tied to a particular place and time ("what", "where", and "when"). It mobilises data from a wide variety of sources, each of which may follow its own convention for which name to use for a given taxon. This can lead to instances where GBIF has multiple records for the same taxon. As an example, consider the western hoolock gibbon. Since its original description as *Simia hoolock* by [Harlan 1834], this gibbon has been variously assigned the genera *Hylobates*, *Bunopithecus*, and most recently, *Hoolock* [Mootnick and Grove 2005], hence the gibbon has been known as *Hylobates hoolock*, *Bunopithecus hoolock*, and *Hoolock hoolock*. Ideally a biodiversity database containing information on this species would be aware of this synonymy and return information on this species regardless of which name the user has chosen. However, GBIF returns different results for each name (Fig. GIBBONS).

![GIBBONS](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/gibbons.png)

Fig. GIBBONS. The three taxonomic names for the western hoolock gibbon in GBIF database, together with the source of the name, the number of occurrence records linked to that name, and a map of those occurrences that have been georeferenced (GBIF data accessed 2013-09-17). Note that the data returned for each name is different.

A user searching GBIF for, say, the name of this gibbon used by the IUCN red list (*Hoolock hoolock*) will think GBIF has virtually no relevant data on the distribution of this taxon. They would need to know about the synonymy of this ape in order to locate the additional data in GBIF that is associated with the other names. Note that this issue is not unique to GBIF: the [Map of Life project](http://www.mappinglife.org/) [Jetz et al. 2012] suffers from the same problem. Searching on the three different taxonomic names for the western hoolock gibbon results in three different maps (Fig. MOL) with no indication that these are for the same taxon.

![MOL](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/mapoflife.png)

Fig MOL. Overlay map showing the three different distributions retrieved from the Map of Life for the three different names for the western hoolock gibbon. Note that these maps needed three separate searches, one for each name.


In addition to reducing the success of users searching for data in GBIF, unrecognised synonyms may potentially impact users' perception of the reliability of GBIF-mobilised data. GBIF users consistently rank taxonomic data (names and checklists) as one of the most important type of data for their needs [Ariño et al. 2013]. It would therefore be desirable if the quality of this data was as high as possible. Based on the white-faced gibbon example (Fig. GIBBONS), and other examples discovered while exploring the GBIF taxonomy [Page 2013b],[Page 2013c] I decided to explore the extent to which GBIF is affected by unrecognised taxonomic synonyms. I have restricted this investigation to animal names so that I could make use of [BioNames](http://bionames.org) [Page 2013d] to investigate particular names in more detail.

## Methods

The current GBIF backbone classification was downloaded as a Darwin Core Archive [Wieczorek et al. 2012] from http://uat.gbif.org/dataset/d7dddbf4-2cf0-4f39-9b2a-bb099caae36c on 4th September 2013 and loaded into a local MySQL database. 

A range of taxa were selected for investigation, based on preliminary browsing of the GBIF classification, and the availability of detailed data on taxonomic names for that taxonomic group. Fig. TERMINOLOGY indicates the terminology for the parts of taxonomic names adopted here, using the example of *Pithecanthropus erectus* Dubois, 1894 and its synonym *Homo erectus* (Dubois, 1894). Note that if we did not know that these  two names were synonyms we could infer it by noting that the two names share the same specific epithet and authorship, and that the genera *Pithecanthropus* and *Homo* are related. This suggests that a quick way of detecting possible synonyms is to find instances of species in different genera within the same family that have the same specific epithet. Given that the specific epithet is often descriptive, or may be named after a person or place, it is not uncommon for two different species in two distinct genera to have the same epithet. We can endeavour to minimise these false matches by including (where available) the taxonomic authority. Names will then only be incorrectly flagged if the same taxonomic authority published used the same epithet for more than one species in the same year. Such false positives do, of course, exist (see example in the results), so names meeting this criterion of belonging to the same family and having the same specific epithet will be termed "problematic" rather than "synonyms". In specific cases, given adequate access to taxonomic databases or the primary literature we can establish whether such problematic taxa are, indeed, synonyms. Conversely, the approached used here may also underestimate the number of problematic taxa. It does not account for variations in spelling caused by either typographic errors or changes in species names required to agree with the gender of the genus name. 

![TERMINOLOGY](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/terminology.png)

Fig. TERMINOLOGY Terminology of taxonomic names. A binomial species name comprises a genus part, a specific epithet, and the authorship. The genus a species is first described in is the "original combination". A species may be subsequently moved to a different genus, resulting in a "new combination".

For each family the accepted species- and subspecies-level in each genus were extracted from the local copy of the GBIF database. Where available, the taxon authorship was appended, after first removing commas and parentheses, the later are used to indicate that the species was not originally described in that genus. For example, given the name *Homo erectus* (Dubois, 1894) we can infer that Dubois did not originally place this species in the genus *Homo* (he described it as *Pithecanthropus erectus* [Dubois 1894]). The parentheses were removed to simplify matching taxon authorship (their presence is also not always a reliable indicator of whether a genus + specific epithet is the original combination or not). 


## Visualisation

Potential inconsistencies in nomenclature within a family-level taxon were visualised using "cluster maps" [Fluit et al. 2006]. Cluster maps are graphs comprising two classes of nodes, one representing a category, the other representing clusters of objects that belong to one or of those categories. All objects that belong in the same category are in the same cluster, and each cluster is connected to each category that its members belong too. In this context the categories are generic names, and the objects being clustered are specific epithets (plus authorship where available).

Fig. CLUSTER shows a cluster map for the three gibbon genera that the hoolock gibbon has been assigned to. The categories are the three generic names (*Bunopithecus*, *Hoolock*, and *Hylobates*), and the clusters contain the species names. The names that occur in combination with only a single genus are in clusters with a single edge linking that cluster to the corresponding genus. For example, there are three names that are unique to *Bunopithecus*, and 27 names that only appear in the genus *Hylobates*. The specific epithet "*hoolock* Harlan 1834" occurs in cluster by itself, linked to the three generic names (*Bunopithecus*, *Hoolock*, and *Hylobates*) with which it has been combined in the GBIF database. Note that there is another species name (*leuconedys* Groves 1967) that occurs in GBIF in more than one genus *Bunopithecus leuconedys* and *Hoolock leuconedys*.

![CLUSTER](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/gibbon-cluster-map.png)

Fig. CLUSTER. Cluster map for the three gibbon genera that the western hoolock gibbon has been assigned to.


## Metrics

To provide a quick way to scan results for possible problematic taxa I developed a number of measures derived from the cluster map. A graph comprises nodes and edges, and the number of edges connected to a node is that node's degree. If there were no problematic taxa then each genus would be connected to a single cluster, so each genus node would have degree = 1 as would each cluster node. The greater the degree of a genus node the greater the number of other genera that it is in contention with.

The greater the degree of a cluster node the more genera are in contention for the species in that cluster (for example, the *hoolock* cluster has degree 3, corresponding to the three rival genera (*Bunopithecus*, *Hoolock*, and *Hylobates*). If there were no problematic species names then the number of species names in the clusters would correspond to the number of taxa. In the gibbon example there were 35 distinct species and subspecies names, but only 32 names are in the clusters. Because the specific epithet "*hoolock*" occurs in three genera it occurs once in a single cluster (3 - 1 = 2), and likewise the name "*leuconedys*" occurs in two genera but a single cluster (2 - 1 = 1), so there are 2 + 1 = 3 fewer names in the clusters than in the original list. 

To locate family-level taxa that may have hidden synonymy, for each family I plotted the number of potentially problematic species names identified by the cluster maps against the total number of accepted species GBIF recognised within that family.

# Results

to do: tables of results

## Amphibia

![Amphibia](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/amphibia-plot.png)

Fig. Amphibia Potentially problematic species names in Amphibia. Each dot corresponds to an amphibian family.

Fig. Amphibia shows a plot of the numbers of potentially problematic names against total number of species for all families in the class Amphibia. Families with no problematic names lie along the x-axis. The most noticeable outlier with 89 potentially problematic names is the frog family Rhacophoridae. The GBIF classification for this family is an amalgam from various sources, namely The Catalogue of Life (CoL), the IUCN Red List of Threatened Species (IUCN), the Interim Register of Marine and Nonmarine Genera (IRMNG), and the Integrated Taxonomic Information System (ITIS), and contains numerous duplicates of species names corresponding to different interpretations of the genus *Philautus*. Generic placement in this family is unstable as new findings from molecular phylogenetics are incorporated [Li et al. 2009]resulting in the break up of *Philautus* into smaller genera. In this case GBIF has aggregated competing classifications resulting in numerous frog species appearing twice.

![Rhacophoridae.png](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/Rhacophoridae.png)

Fig. Rhacophoridae Cluster map for selected genera of the frog family Rhacophoridae. 

The other noticeable outlier is the family Bufonidae, which is also being revised in light of recent phylogenetic analyses.

## Mammals

![Mammalia](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/mammalia-plot.png)

Fig. Mammalia Potentially problematic species names in Mammalia.

There are a total of 556 problematic species names for mammals, the three families with the largest number of issues are the Muridae (rats and mice), and bat families Vespertilionidae and Molossidae (Fig. Mammalia). As an example Fig. Tadarida shows the cluster map for *Tadarida* (family Molossidae) and associated genera. Generic limits within the Molossidae vary among classifications, and some names have at various times been alternately demoted to subgeneric or promoted to generic rank. Recent work [Jones et al. 2002]suggests that some of these subgenera are not necessarily closely related to the genera they have been subsumed by. The classifications merged by GBIF represent different historical snapshots of bat classification, resulting in duplicate taxa.

![Tadarida](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/tadarida.png)

Fig. Tadarida Cluster map showing conflicting generic limits for three genera in the family Molossidae.

## Insects

Across insects some 56,999 species names are potentially problematic (Fig. Insecta). This number represents approximately five percent of the total number of insect species in GBIF.

![Insecta](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/insects-plot.png)

Fig. Insecta Potentially problematic species names in insects.

The families with the largest number of problematic names are the Noctuidae (Lepidoptera), Cerambycidae (Coleoptera), and Tachinidae (Diptera). These are large families so this is perhaps expected, however some families have disproportionately high numbers of potential issues, such as the fly families Syrphidae and Tachinidae.

The fruit fly family Drosophilidae, a well known source of nomenclatural issues [Dalton 2010], was explored further. Generic limits in this family vary among classifications which may also differ on whether given name deserves generic or merely subgeneric rank. As a consequence the genus *Drosophila* is connected to 39 different clusters of species names, the largest comprising names only associated with Drosophila in the GBIF classification. As but one example of a problematic names, GBIF has both *Drosophila serriflabella* Okada, 1966 and *Lordiphosa serriflabella* (Okada, 1966) (from IRMNG and CoL, respectively). This fly was first described as *Drosophila (Sophophora) serriflabella* by [Okada 1966], who subsequently [Okada 1984] transferred it to the *Drosophila* subgenus *Lordiphosa* resulting in the name *Drosophila (Lordiphosa) serriflabella*. [Grimaldi 1990] elevated *Lordiphosa* to genus level, so the name becomes *Lordiphosa serriflabella*. GBIF has just two of the various names assigned to this fly, and is unaware that the names are synonyms.

## Typographic errors

Although the method used here cannot handle changes in the spelling of the specific epithet, it can detect some spelling inconsistencies in generic names. For example, in the wasp family Evaniidae GBIF has two generic names, *Szepligetella* sourced from The Catalogue of Life, 3rd January 2011 and *Szepligetiella* (note the extra "i") sourced from the Australian Faunal Directory via the Interim Register of Marine and Nonmarine Genera [Rees 2006-present] (Fig. Evaniidae). The correct spelling is  *Szepligetella* (see [Bradley 1908]), so the 14 species belonging to *Szepligetiella* duplicate the 14 from *Szepligetella*. Note that one specific epithet (*similis* Szépligeti 1903) is shared with a third genus *Hyptia*. This is a false positive, in that *Szepligetella similis* was originally described as *Evania similis* and *Hyptia similis* is the original name for a different species (both *Evania similis* and *Hyptia similis* were described in the same publication on pages 385 and 376 respectively; [Szépligeti 1903]).

![Evaniidae](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/Evaniidae.png)

Fig. Evaniidae. Cluster map for three wasp genera in the family Evaniidae. 


# Discussion

The quality of GBIF data has come under increasing scrutiny [Yesson et al. 2007],[Gaiji et al. 2013],[Mesibov, 2013],[Otegui et al. 2013a]. Given the scale of GBIF's ambition (mobilising the world's biodiversity data), the multiplicity of data providers, and variation in the quality of that data, it is inevitable that there will be errors. By themselves, the presence of errors matters less than the speed with which they are identified and corrected [Birney 2012]. Furthermore, errors may exist but not have a significant impact on the kinds of uses to which the data are being put [Belbin et al. 2013].

But the inconsistency in the GBIF taxonomy is worrying, particularly as its extent seems unrecognised. GBIF is dependent on the quality of its source data bases, but many taxonomic databases are little more than lists of names that have an imprimatur of authority stamped on them. The names themselves are rarely linked to the primary taxonomic literature, making it difficult for a user to investigate further and establish the status of a contested name. Given that we have multiple databases and multiple authorities, reconciling name lists becomes an exercise in comparative trust (which authority is more authoritative?). The very existence of multiple "authoritative" sources for the taxonomy of the same group of organisms suggests that different authorities may have different goals and hence different classifications. In the case of mammals, there are at least three major sources of names harvested by GBIF: the Catalogue of Life, the IUCN Red List, and Mammal Species of the World. A cluster map of the accepted species names recognised by each database (Fig. Mammals) reveals that all three sources recognise names that are not recognised by either of the other two databases.

![Mammals](https://raw.github.com/rdmpage/taxonomy-GBIF-manuscript/master/figures/mammal-species.png)

Fig. Mammals. Cluster map for accepted species names for mammals according to three different taxonomic databases. The largest cluster represents names found in all three databases. Clusters linked to a single database are unique to that source.

Unless one database is more up to date than another and includes newly described species (and new mammals are still being discovered; [Ceballos and Ehrlich 2009]), or there limits of species-level taxa are hotly contested (unlikely in the case of most mammals) then we might expect that in this case the names unique to one or more database are objective synonyms. Failure to recognise these has resulted in hundreds of problematic mammalian names in GBIF's classification.

Anecdotally several factors contribute to a multiplicity of names. Taxa that feature in several databases often have unrecognised synonyms. Typically names may occur in a larger database, such as Catalogue of Life, but GBIF also includes additional taxon- or theme-focussed databases, and these may have different classifications. Taxa where subgenera have been employed are often problematic. While some have argued for the utility of subgenera as a way to add information to a name without changing the genus + specific epithet binomial [Harris and Carretero 2003],[Wallach et al. 2009], in practice subgenera promote ambiguity. A name may be written with or without the subgenus, a species name may be assigned to a different subgenus, or a subgenus may be elevated to generic level (both these happened to *Drosophila (Sophophora) serriflabella* discussed above). Anything that increases the number of ways a name can be written adds to the burden of those aggregating data.

## Future directions

There are some practical steps that GBIF could take to help reduce the impact of unrecognised synonyms. It could add additional taxonomic databases that explicitly include synonymy data. It may be that existing providers have this data already. For example, the IUCN Red List of threatened species dataset http://uat.gbif.org/dataset/19491596-35ae-4a91-9a98-85cf505f1bd3 does include synonym data that does not appear to have been harvested by GBIF. It may also need to apply more stringent filters to some data sources. Some names in GBIF appear to be database artefacts or misspellings. These may be caught by more sophisticated data cleaning methods.

But the larger task facing the zoological taxonomy community is the lack of one of the most basic requirements for successful data integration using taxonomic names, a database of synonyms. If taxonomic practice makes it inevitable that name changes will occur as species are shuffled around genera, then arguably it is incumbent on the community to provide tools that shield the broader biological community from the consequences of that practice.

# Acknowledgements

# References

- Ariño, A., Chavan, V., & Faith, D. (2013). Assessment of user needs of primary biodiversity data: Analysis, Concerns, and Challenges. Biodiversity Informatics, 8(2). Retrieved from https://journals.ku.edu/index.php/jbi/article/view/4094/4199 [Ariño et al. 2013]

- Bradley, J Chester (1908) The Evaniidae, ensign flies, an archaic family of Hymenoptera. Transactions of the American Entomological Society Philadelphia 34: 101–194. http://biostor.org/reference/107168 [Bradley 1908]

- Belbin, L., Daly, J., Hirsch, T., Hobern, D., & LaSalle, J. (2013). A specialist’s audit of aggregated occurrence records: An “aggregator”s’ perspective. ZooKeys, 305, 67–76. [doi:10.3897/zookeys.305.5438] [Belbin 2013]

- Birney, E. (2012). The making of ENCODE: Lessons for big-data projects. Nature, 489(7414), 49–51. [doi:10.1038/489049a][Birney 2012]

- Ceballos, G., & Ehrlich, P. R. (2009). Discoveries of new mammal species and their implications for conservation and ecosystem services. Proceedings of the National Academy of Sciences, 106(10), 3841–3846. [doi:10.1073/pnas.0812419106][Ceballos and Ehrlich 2009]

- Dalton, R. (2010). What’s in a name? Fly world is abuzz. Nature, 464(7290), 825–825. [doi:10.1038/464825a][Dalton 2010]

- Dubois, E. (1894) Pithecanthropus Erectus. Eine menschenaehnliche Uebergangsform aus Java. [doi: 10.5962/bhl.title.59381] [Dubois 1894]

- Fluit, C., Sabou, M., & Harmelen, F. (2006). Visualizing the Semantic Web. (V. Geroimenko & C. Chen, Eds.) (pp. 45–58). Springer Science + Business Media. [doi:10.1007/1-84628-290-X_3][Fluit et al. 2006]

- Samy Gaiji, Vishwas Chavan, Arturo H. Ariño, Javier Otegui, Donald Hobern, Rajesh Sood, Estrella Robles (2013) Content assessment of the primary biodiversity data published through GBIF network: status, challenges and potentials. Biodiversity Informatics, 8:94-172. https://journals.ku.edu/index.php/jbi/article/view/4124 [Gaiji et al. 2013]

- Grimaldi, D. (1990). A phylogenetic, revised classification of genera in the Drosophilidae (Diptera) Bulletin of the American Museum of Natural History 197: 1–139. http://hdl.handle.net/2246/888 [Grimaldi 1990]

- Harlan R (1834) Description of a Species of Orang, from the north-eastern province of British East India, lately the kingdom of Assam. Transactions of the American Philosophical Society 4: 52–59. http://biostor.org/reference/127799 [Harlan 1834]

- Harris, D. R. and M.A. Carretero. (2003).Amphibia-Reptilia, 24(1), 119–122. [doi:10.1163/156853803763806975] [Harris and Carretero 2003]

- Jetz, W., McPherson, J. M., & Guralnick, R. P. (2012). Integrating biodiversity distribution knowledge: toward a global map of life. Trends in Ecology & Evolution, 27(3), 151–159. [doi:10.1016/j.tree.2011.09.007] [Jetz et al. 2012]

- JONES, K. E., PURVIS, A., MacLARNON, A., BININDA-EMONDS, O. R. P., & SIMMONS, N. B. (2002). A phylogenetic supertree of the bats (Mammalia: Chiroptera). Biological Reviews of the Cambridge Philosophical Society, 77(2), 223–259. [doi:10.1017/S1464793101005899] [Jones et al. 2002]

- Kennedy, J. B., Kukla, R., & Paterson, T. (2005). Scientific Names Are Ambiguous as Identifiers for Biological Taxa: Their Context and Definition Are Required for Accurate Data Integration (pp. 80–95). Springer-Verlag. [doi:10.1007/11530084_8][Kennedy et al. 2005]

- Li, J., Che, J., Murphy, R. W., Zhao, H., Zhao, E., Rao, D., & Zhang, Y. (2009). New insights to the molecular phylogenetics and generic assessment in the Rhacophoridae (Amphibia: Anura) based on five nuclear and three mitochondrial genes, with comments on the evolution of reproduction. Molecular Phylogenetics and Evolution, 53(2), 509–522. [doi:10.1016/j.ympev.2009.06.023] [Li et al. 2009]

- Matthew, W. D., Granger, W. (1923) New fossil Mammals from the Pliocene of Szechuan, China. Bulletin of the American Museum of Natural History 48: 563–598. http://hdl.handle.net/2246/1308 [Matthew and Granger 1923]

- Mesibov, R. (2013). A specialist’s audit of aggregated occurrence records. ZooKeys, 293(0), 1–18. doi:10.3897/zookeys.293.5111

- Mootnick A, Groves C (2005) A new generic name for the hoolock gibbon (Hylobatidae). International Journal of Primatology 26(4): 971–976. [doi:10.1007/s10764-005-5332-4] [Mootnick and Groves 2005]

- Okada, T. (1966) Diptera from Nepal. Cryptochaetidae, Diastatidae & Drosophilidae. Bulletin of the British Museum (Natural History) Entomology Suppl 6: 1–129.http://biostor.org/reference/113429 [Okada 1966]

- OKADA, Toyohi (1984) New or little known species of Drosophila (Lordiphosa) with taximetrical analyses (Diptera, Drosophilidae). Kontyu 52(4): 565–575. http://ci.nii.ac.jp/naid/110003502235 [Okada 1984]

- Otegui, Javier, Arturo H. Ariño, Vishwas Chavan, Samy Gaiji (2013a) On the dates of GBIF mobilised primary biodiversity records. Biodiversity Informatics 8(2): 173-184. https://journals.ku.edu/index.php/jbi/article/view/4125 [Otegui 2013a]

- Otegui, J., Ariño, A. H., Encinas, M. A., & Pando, F. (2013b). Assessing the Primary Data Hosted by the Spanish Node of the Global Biodiversity Information Facility (GBIF). (G. P. S. Raghava, Ed.)PLoS ONE, 8(1), e55144. [doi:10.1371/journal.pone.0055144][Otegui 2013b]

- Page, R. D. M. 2013a. Gibbons and GBIF: good grief what a mess http://iphylo.blogspot.co.uk/2013/06/gibbons-and-gbif-good-grief-what-mess.html [Page 2013a]

- Page, R. D. M. 2013b. More GBIF taxonomy fail. http://iphylo.blogspot.co.uk/2013/06/more-gbif-fail.html [Page 2013b]

- Page, R. D. M. 2013c.Cluster maps, papaya plots, and the trouble with GBIF taxonomy. http://iphylo.blogspot.co.uk/2013/08/cluster-maps-papaya-plots-and-trouble.html [Page 2013c]

- Roderic D M Page. (2013d). BioNames: linking taxonomy, texts, and trees. PeerJ Inc. [doi:10.7287/peerj.preprints.54v1] [Page 2013d]

- Patterson, D. J., Cooper, J., Kirk, P. M., Pyle, R. L., & Remsen, D. P. (2010). Names are key to the big new biology. Trends in Ecology & Evolution, 25(12), 686–691. [doi:10.1016/j.tree.2010.09.004][Patterson et al. 2010]

- Prouty, L. A., Buchanan, P. D., Pollitzer, W. S., & Mootnick, A. R. (1983). Taxonomic note:Bunopithecus: A genus-level taxon for the hoolock gibbon (Hylobates hoolock). American Journal of Primatology, 5(1), 83–87. [doi:10.1002/ajp.1350050110][Prouty et al. 1983]

- Rees, T. (compiler), 2006-present. The Interim Register of Marine and Nonmarine Genera (IRMNG). Available at http://www.obis.org.au/irmng/ . (Date accessed: 23-09-2013) [http://www.obis.org.au/irmng/][Rees 2006-present]

- Sarkar, I. N. (2007). Biodiversity informatics: organizing and linking information across the spectrum of life. Briefings in Bioinformatics, 8(5), 347–357. [doi:10.1093/bib/bbm037][Sarkar 2007]

- Szépligeti, G. 1903. Neue Evaniiden aus der Sammlung des Ungerischen National-Museums. Annales Musei Nationalis Hungarici 1: 364-395. http://publication.nhmus.hu/pdf/annHNHM/Annals_HNHM_1903_Vol_1_364.pdf [Szépligeti 1903]

- Taylor, R. 2001. A step at a time: New Zealand’s progress toward hominid rights. Animal Law Review, 7:35-43. http://www.animallaw.info/journals/jo_pdf/lralvol_7p35.pdf [Taylor 2001]

- Wallach, Van, Wolfgang Wüster, and Donald G. Broadley (2009). In praise of subgenera: taxonomic status of cobras of the genus *Naja* Laurenti (Serpentes: Elapidae). Zootaxa 2236: 26–36 (2009) [Wallach et al. 2009] http://www.mapress.com/zootaxa/2009/f/zt02236p036.pdf

- Wieczorek, J., Bloom, D., Guralnick, R., Blum, S., Döring, M., Giovanni, R., Robertson, T., et al. (2012). Darwin Core: An Evolving Community-Developed Biodiversity Data Standard. (I. N. Sarkar, Ed.)PLoS ONE, 7(1), e29715. [doi:10.1371/journal.pone.0029715][Wieczorek et al. 2012]

- Wildman, D. E.,  Monica Uddin, Guozhen Liu†, Lawrence I. Grossman, and Morris Goodman (2003). Implications of natural selection in shaping 99.4% nonsynonymous DNA identity between humans and chimpanzees: Enlarging genus Homo. Proceedings of the National Academy of Sciences, 100(12), 7181-7188. Proceedings of the National Academy of Sciences. [doi:10.1073/pnas.1232172100] [Wildman et al. 2003]

- Yesson, C., Brewer, P. W., Sutton, T., Caithness, N., Pahwa, J. S., Burgess, M., Gray, W. A., et al. (2007). How Global Is the Global Biodiversity Information Facility? (J. Beach, Ed.)PLoS ONE, 2(11), e1124. [doi:10.1371/journal.pone.0001124][Yesson et al. 2007]

[Ariño et al. 2013]: https://journals.ku.edu/index.php/jbi/article/view/4094/4199 
[Belbin 2013]: http://dx.doi.org/10.3897/zookeys.305.5438
[Birney 2012]: http://dx.doi.org/doi:10.1038/489049a
[Bradley 1908]: http://biostor.org/reference/107168
[Ceballos and Ehrlich 2009]: http://dx.doi.org/10.1073/pnas.0812419106
[Dalton 2010]: http://dx.doi.org/10.1038/464825a
[Dubois 1894]: http://dx.doi.org/10.5962/bhl.title.59381
[Fluit et al. 2006]: http://dx.doi.org/10.1007/1-84628-290-X_3
[Gaiji et al. 2013]: https://journals.ku.edu/index.php/jbi/article/view/4124 
[Grimaldi 1990]: http://hdl.handle.net/2246/888 
[Harlan 1834]: http://biostor.org/reference/127799 
[Harris and Carretero 2003]: http://dx.doi.org/10.1163/156853803763806975 
[Jetz et al. 2012]: http://dx.doi.org/10.1016/j.tree.2011.09.007
[Jones et al. 2002]: http://dx.doi.org/10.1017/S1464793101005899
[Kennedy et al. 2005]: http://dx.doi.org/10.1007/11530084_8
[Li et al. 2009]: http://dx.doi.org/10.1016/j.ympev.2009.06.023
[Matthew and Granger 1923]: http://hdl.handle.net/2246/1308
[Mootnick and Groves 2005]: http://dx.doi.org/10.1007/s10764-005-5332-4
[Okada 1966]: http://biostor.org/reference/113429
[Okada 1984]: http://ci.nii.ac.jp/naid/110003502235 
[Otegui 2013a]: https://journals.ku.edu/index.php/jbi/article/view/4125 
[Otegui 2013b]: http://dx.doi.org/10.1371/journal.pone.0055144
[Page 2013a]: http://iphylo.blogspot.co.uk/2013/06/gibbons-and-gbif-good-grief-what-mess.html 
[Page 2013b]: http://iphylo.blogspot.co.uk/2013/06/more-gbif-fail.html
[Page 2013c]: http://iphylo.blogspot.co.uk/2013/08/cluster-maps-papaya-plots-and-trouble.html 
[Page 2013d]: http://dx.doi.org/10.7287/peerj.preprints.54v1
[Patterson et al. 2010]: http://dx.doi.org/10.1016/j.tree.2010.09.004
[Prouty et al. 1983]: http://dx.doi.org/10.1002/ajp.1350050110
[Rees 2006-present]: http://www.obis.org.au/irmng/
[Sarkar 2007]: http://dx.doi.org/10.1093/bib/bbm037
[Szépligeti 1903]: http://publication.nhmus.hu/pdf/annHNHM/Annals_HNHM_1903_Vol_1_364.pdf
[Thau and Ludäscher 2007]: http://dx.doi.org/10.1016/j.ecoinf.2007.07.005
[Wildman et al. 2003]: http://dx.doi.org/10.1073/pnas.1232172100
[Taylor 2001]: http://www.animallaw.info/journals/jo_pdf/lralvol_7p35.pdf
[Wallach et al. 2009]: http://www.mapress.com/zootaxa/2009/f/zt02236p036.pdf
[Wieczorek et al. 2012]: http://dx.doi.org/10.1371/journal.pone.0029715
[Yesson et al. 2007]: http://dx.doi.org/10.1371/journal.pone.0001124

