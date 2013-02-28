## Errors

The following tests are excluded of various reasons:

* disambiguate_YearSuffixWithEtAlSubequent.txt, disambiguate_YearCollapseWithInstitution.txt
    > Both need the cite-group-delimiter set to the _layout delimiter_, but *sort_GroupedByAuthorstring.txt*
    needs it set to *', '*
* date_NonexistentSortReverseCitation.txt, date_SortEmptyDatesCitation.txt
    > In 'date_NonexistentSortReverseBibliography.txt' the order is 'Item 2; Item 4; Item 1; Item 3', because
    empty dates are put to the end. So in *'date_NonexistentSortReverseCitation.txt'* the order couldn't be the same,
    if empty dates "have a value equivalent to zero", which would lead to 0 <= 0 <= 19990215 <= 20000215, which
    results in  'Item 1; Item 3; Item 2; Item 4'. Or what does "equivalent to zero" mean if not a real zero (0)
* position_ResetNoteNumbers.txt
    > Note [2] and [3] refere to ITEM-3 so note [3] should be **..[3] ibid** and not **..[3] Book C (note 1)**
* position_IbidSeparateCiteSameNote.txt
    > Don't know how to figure out to use "page" as "label".
* position_IbidInText.txt
    > There is only one cite in this citation ([2]) and ITEM-1 is not the same as ITEM-2 in the previous citation, so
    shouldn't it be **>>[2] Appleton “Tom Swift and His Electric Runabout” (1910).** instead of **>>[2] Ibid.**?
* position_IbidWithMultipleSoloCitesInBackref.txt
    > Does this test mean that only one **ibid** is allowed and that the ibid position is only valid if the next entry
    has not the same item-id?
* bugreports_UnisaHarvardInitialization.txt
    > Missing locale file.
* date_LoneJapaneseMonth.txt
    > I'm not sure if this possible in php.
* sort_ChangeInNameSort.txt
    > Input and Input2 set, don't know how to handle that
* bugreports_ContainerTitleShort.txt
    > Not clear how to remove the dots in journalAbbreviation.
* date_YearSuffixImplicitWithNoDate.txt, date_YearSuffixWithNoDate.txt
    > I don't know how to figure out what is part of a cite.
* sort_WithAndInOneEntry.txt
    > I don't know how to handle and terms while sorting names.
* date_YearSuffixImplicitWithNoDateOneOnly.txt
    > I don't know why use form numeric on date and not text.
* disambiguate_InitializeWithButNoDisambiguation.txt
    > I don't know how to determine the year delimiter
* disambiguate_YearSuffixMidInsert.txt
    > UN DESA 2011c should be the first value not the last, if sorted by bibliography keys.
* collapse_CitationNumberRangesInsert.txt
    > Ignoring ambiguous values for "citationID".
* group_ShortOutputOnly.txt
    > Don't know why it is not uppercase.
* group_ComplexNesting.txt
    > Shouldn't it be **Doe Inc. Retrieved June 1, 1965, from http://example.com/** instead of
    **Doe Inc. (n.d.). Retrieved June 1, 1965, from http://example.com/**, because the group in the macro "issued" is
    accessing the empty variable **year-suffix** and should be suppressed.
* discretionary_CitationNumberAuthorOnlyThenSuppressAuthor.txt
    > Shouldn't be there a translation option or a term for **Reference**?
* discretionary_CitationNumberAuthorOnlyThenSuppressAuthor.txt, discretionary_CitationNumberSuppressAuthor.txt
    > In **discretionary_CitationNumberSuppressAuthor.txt** the citation-number is rendered if **suppress-author** is
     used, but in **discretionary_CitationNumberAuthorOnlyThenSuppressAuthor.txt** with the same style and data it is
     rendered.

