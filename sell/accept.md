# [Accepting Bitcoin](/sell) with your Online Business

Depending on your country, Bitcoin has payment processors that can let you accept 
Bitcoin and receive USD/etc directly into your account. Some existing CMSes, 
depending on your industry, implement Bitcoin payments as well.

Otherwise there are generally two ways to accept payments with an arbitrary coin from scratch:

1. Keep a database of addresses, assign these addresses to payments, and use a block explorer 
   API to regularly check the address for payment. This is the most secure solution but requires more 
   administration to keep the database clean and full of addresses.
   
1. Run an actual coin client (e.g. `bitcoind`) and request new addresses through JSON-RPC, and interact 
   with the client regularly to check for payment. This is the least secure solution because you are running 
  a wallet on a separate machine, but requires the least work long-term because everything is automated.

You need to decide if you support zero-confirmation payments or not. This decision will depend on if you can 
reverse payments for your product, on the convenience you wish to offer customers, and on the value of the 
payments you are accepting.

Regular billing is not supported directly by any of the major coins but there is some work coming up in the 
crypto space on contracts that allow this kind of thing. In the conventional space you would send an e-mail 
or message with a new Bitcoin address every month/year to renew.
