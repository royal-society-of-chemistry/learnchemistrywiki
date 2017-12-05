	function broadenSearchOrReturnNoResults(searchterm) {
		//search on first part of inchikey but only if it\'s not been fed through here before
		if(searchterm.length >20)
			{
			processResults(searchterm.substr(0,searchterm.indexOf("-")+1));
			}
		else
			outputText("outputdiv", "No results for this molecule or similar");
	}
	