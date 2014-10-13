#Turkish Identity Validation Class

The Validation class for Turkish Identification (tcKimlikNo) Number using SOAP Web Service Client.
Author: Midori Kocak [Midori Kocak Website](http://www.mtkocak.net)

##Validation Algorithm
* This class first checks if the tcKimlikNo passes the validation algorithm:
* tcKimlikNo is a number with 11 digits.
* First digit can't be 0.
* Sum of 1, 3, 5, 7 and 9th digits are multiplied by 7. And sum of 2, 4, 6, and 8th digits are subtracted
* from this value. The modulus 10 of this value is the tenth digit.
* Sum of 1, 2, 3, 4, 5, 6, 7, 8, 9, and 10th digit's modulus 10 is the eleventh digit.

## Soap Client
* State confirmation of the Turkish Identity Number
* Soap Client for checking the Turkish Identity Number communicating with the Turkish Identification identity.


## Usage
 * Validate: $kimlik->validate();
 * Ask to state: $kimlik->askToState($name,$familyName,$birthYear);
