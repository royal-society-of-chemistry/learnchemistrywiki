	function doesInChIMatch(divname, inchi, correctinchi, correctmessage, wrongstdinchi1, warningmessage1, wrongstdinchi2, warningmessage2, wrongstdinchi3, warningmessage3) {
		if(inchi==correctinchi)
		{
		  if(correctmessage!="")
		  {
		    outputText(divname, correctmessage);
		    return;
		  } else {
		    outputText(divname, "Right answer - Well Done!");
		    return;
	      }
		}
		if(wrongstdinchi1 !="")
		  {
		  if (inchi==wrongstdinchi1)
		     {
			    outputWarning(divname, warningmessage1);
				outputWarning(divname, "View |onclick:showrightmol:a:correct answer|" );
				return;
			 }
		  }
		if(wrongstdinchi2 !="")
		  {
		  if (inchi==wrongstdinchi2)
		     {
			    outputWarning(divname, warningmessage2);
				outputWarning(divname, "View |onclick:showrightmol:a:correct answer|" );
				return;
			 }
		  }
		if(wrongstdinchi3 !="")
		  {
		  if (inchi==wrongstdinchi3)
		     {
			    outputWarning(divname, warningmessage3);
				outputWarning(divname, "View |onclick:showrightmol:a:correct answer|" );
				return;
			 }
		  }
		var splitinchi =inchi.split("/");
		var splitcorrectinchi =correctinchi.split("/");
		/*Matching formula, c and h layer so just stereochemistry is wrong*/
		if((splitinchi[1]+splitinchi[2]+splitinchi[3])==(splitcorrectinchi[1]+splitcorrectinchi[2]+splitcorrectinchi[3]))
		  {
			outputWarning(divname, "You are very close - you have everything right apart from the stereochemistry. Try again or view |onclick:showrightmol:a:correct answer|");
			return;
		  }
		/*Matching formula and c layer */
		if((splitinchi[1]+splitinchi[2])==(splitcorrectinchi[1]+splitcorrectinchi[2]))
		  {
			outputWarning(divname, "You are very close - you have drawn a molecule with the right formula and skeleton. Try again or view |onclick:showrightmol:a:correct answer|");
			return;
		  }
		/*Matching formula */
		if((splitinchi[1])==(splitcorrectinchi[1]))
		  {
			outputWarning(divname, "You have drawn an isomer of the right answer, but need to revise the skeleton. Try again or view |onclick:showrightmol:a:correct answer|");
			return;
		  }
		/*Matching c layer */
		if((splitinchi[2])==(splitcorrectinchi[2]))
		  {
			outputWarning(divname, "You have drawn a molecule with the right skeleton but the wrong formula. Try again or view |onclick:showrightmol:a:correct answer|");
			return;
		  }
		outputWarning(divname, "Wrong answer - please try again or view |onclick:showrightmol:a:correct answer|");
	}
	