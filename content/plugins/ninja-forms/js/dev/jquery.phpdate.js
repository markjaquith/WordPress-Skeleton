/*
  PHP style date() plugin
  Call in exactly the same way as you do the "date" command in PHP
  e.g. s = $.PHPDate("l, jS F Y", dtDate);

  License:
  PHPDate 1.0 jQuery Plugin

  Copyright (c) 2008 Jon Combe (http://joncom.be)

  Permission is hereby granted, free of charge, to any person
  obtaining a copy of this software and associated documentation
  files (the "Software"), to deal in the Software without
  restriction, including without limitation the rights to use,
  copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the
  Software is furnished to do so, subject to the following
  conditions:

  The above copyright notice and this permission notice shall be
  included in all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
  OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
  HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
  WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
  FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
  OTHER DEALINGS IN THE SOFTWARE.
*/

(function($) {
  var aDays = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
  var aMonths = ["January","February","March","April","May","June","July","August","September","October","November","December"];

  // main function
  $.PHPDate = function(sString, dtDate) {
    var sElement = "";
    var sOutput = "";

    // we can cheat with "r"...
    sString = sString.replace(/r/g, "D, j M Y H;i:s O");

    // loop through string
    for (var i = 0; i < sString.length; i++) {
      sElement = sString.charAt(i);
      switch (sElement) {
        case "a": sElement = AMPM(dtDate.getHours()); break;
        case "c":
          sElement = (dtDate.getFullYear() + "-" +
                      AddLeadingZero(dtDate.getMonth()) + "-" +
                      AddLeadingZero(dtDate.getDate()) + "T" +
                      AddLeadingZero(dtDate.getHours()) + ":" +
                      AddLeadingZero(dtDate.getMinutes()) + ":" +
                      AddLeadingZero(dtDate.getSeconds()));
          var sTemp = dtDate.toString().split(" ")[5];
          if (sTemp.indexOf("-") > -1) {
            sElement += sTemp.substr(sTemp.indexOf("-"));
          } else if (sTemp.indexOf("+") > -1) {
            sElement += sTemp.substr(sTemp.indexOf("+"));
          } else {
            sElement += "+0000";
          }
          break;
        case "d": sElement = AddLeadingZero(dtDate.getDate()); break;
        case "g": sElement = TwelveHourClock(dtDate.getHours()); break;
        case "h": sElement = AddLeadingZero(TwelveHourClock(dtDate.getHours())); break;
        case "i": sElement = AddLeadingZero(dtDate.getMinutes()); break;
        case "j": sElement = dtDate.getDate(); break;
        case "l": sElement = aDays[dtDate.getDay()]; break;
        case "m": sElement = AddLeadingZero(dtDate.getMonth() + 1); break;
        case "n": sElement = dtDate.getMonth() + 1; break;
        case "o": (new Date(FirstMonday(dtDate.getFullYear())) > dtDate) ? sElement = (dtDate.getFullYear() - 1) : sElement = dtDate.getFullYear(); break;
        case "s": sElement = AddLeadingZero(dtDate.getSeconds()); break;
        case "t":
          var dtTemp = new Date(dtDate.valueOf());
          dtTemp.setMonth(dtTemp.getMonth() + 1)
          dtTemp.setDate(0);
          sElement = dtTemp.getDate();
          break;
        case "u": sElement = dtDate.getMilliseconds(); break;
        case "w": sElement = dtDate.getDay(); break;
        case "y": sElement = dtDate.getFullYear().toString().substr(2, 2); break;
        case "z":
          var dtFirst = new Date(dtDate.getFullYear(), 0, 1, 0, 0, 0, 0);
          var dtLast = new Date(dtDate.getFullYear(), dtDate.getMonth(), dtDate.getDate(), 0, 0, 0, 0);
          sElement = Math.round((dtLast.valueOf() - dtFirst.valueOf()) / 1000 / 60 / 60/ 24);
          break;
        case "A": sElement = AMPM(dtDate.getHours()).toUpperCase(); break;
        case "B":
          sElement = Math.floor(((dtDate.getHours() * 60 * 60 * 1000) +
          (dtDate.getMinutes() * 60 * 1000) +
          (dtDate.getSeconds() * 1000) +
          (dtDate.getMilliseconds())) / 86400);
          break;
        case "D": sElement = aDays[dtDate.getDay()].substr(0, 3); break;
        case "F": sElement = aMonths[dtDate.getMonth()]; break;
        case "G": sElement = dtDate.getHours(); break;
        case "H": sElement = AddLeadingZero(dtDate.getHours()); break;
        case "I":
          var dtTempFirst = new Date(dtDate.getFullYear(), 0, 1);
          var dtTempLast = new Date(dtDate.getFullYear(), dtDate.getMonth(), dtDate.getDate());
          var iDaysDiff = (dtTempLast.valueOf() - dtTempFirst.valueOf()) / 1000 / 60 / 60 / 24;
          (iDaysDiff == Math.round(iDaysDiff)) ? sElement = 0 : sElement = 1;
          break;
        case "L": ((new Date(dtDate.getFullYear(), 2, 0)).getDate() == 29) ? sElement = 1 : sElement = 0; break;
        case "M": sElement = aMonths[dtDate.getMonth()].substr(0, 3); break;
        case "N": (dtDate.getDay() == 0) ? sElement = 7 : sElement = dtDate.getDay(); break;
        case "O":
          var sTemp = dtDate.toString().split(" ")[5];
          if (sTemp.indexOf("-") > -1) {
            sElement = sTemp.substr(sTemp.indexOf("-"));
          } else if (sTemp.indexOf("+") > -1) {
            sElement = sTemp.substr(sTemp.indexOf("+"));
          } else {
            sElement = "+0000";
          }
          break;
        case "P":
          var sTemp = dtDate.toString().split(" ")[5];
          if (sTemp.indexOf("-") > -1) {
            var aTemp = sTemp.substr(sTemp.indexOf("-") + 1).split("");
            sElement = ("-" + aTemp[0] + aTemp[1] + ":" + aTemp[2] + aTemp[3]);
          } else if (sTemp.indexOf("+") > -1) {
            var aTemp = sTemp.substr(sTemp.indexOf("+") + 1).split("");
            sElement = ("+" + aTemp[0] + aTemp[1] + ":" + aTemp[2] + aTemp[3]);
          } else {
            sElement = "+00:00";
          }
          break;
        case "S": sElement = DateSuffix(dtDate.getDate()); break;
        case "T":
          sElement = dtDate.toString().split(" ")[5];
          if (sElement.indexOf("+") > -1) {
            sElement = sElement.substr(0, sElement.indexOf("+"));
          } else if (sElement.indexOf("-") > -1) {
            sElement = sElement.substr(0, sElement.indexOf("-"));
          }
          break;
        case "U": sElement = Math.floor(dtDate.getTime() / 1000); break;
        case "W":
          var dtTempFirst = new Date(FirstMonday(dtDate.getFullYear()));
          var dtTempLast = new Date(dtDate.getFullYear(), dtDate.getMonth(), dtDate.getDate());
          sElement = Math.ceil(Math.round((dtTempLast.valueOf() - dtTempFirst.valueOf()) / 1000 / 60 / 60/ 24) / 7);
          break;
        case "Y": sElement = dtDate.getFullYear(); break;
        case "Z":
          (dtDate.getTimezoneOffset() < 0) ? sElement = Math.abs(dtDate.getTimezoneOffset() * 60) : sElement = (0 - (dtDate.getTimezoneOffset() * 60));
          break;
      }
      sOutput += sElement.toString();
      }
    return sOutput;
  }

  // add leading zero
  function AddLeadingZero(iValue) {
    if (iValue < 10) {
      iValue = ("0" + iValue);
    }
    return iValue;
  }

  // Ante meridiem and Post meridiem
  function AMPM(iHours) {
    if (iHours > 11) {
      return "pm";
    } else {
      return "am";
    }
  }

  // date suffix
  function DateSuffix(iDay) {
    var sSuffix = "th";
    switch (parseInt(iDay)) {
      case 1:
      case 21:
      case 31:
        sSuffix = "st";
        break;
      case 2:
      case 22:
        sSuffix = "nd";
        break;
      case 3:
      case 23:
        sSuffix = "rd";
    }
    return sSuffix;
  }

  // find the first Monday in a given year (for ISO 8601 dates)
  function FirstMonday(iYear) {
    var dtTemp = new Date(iYear, 0, 1);
    while (dtTemp.getDay() != 1) {
      dtTemp.setDate(dtTemp.getDate() + 1);
    }
    return dtTemp.valueOf();
  }

  // 12-Hour clock
  function TwelveHourClock(iHours) {
    if (iHours == 0) {
      iHours = 24;
    } else if (iHours > 12) {
      iHours -= 12;
    }
    return iHours;
  }
})(jQuery);