from trac.core import *
from trac.web.api import IRequestFilter, ITemplateStreamFilter

from trac.wiki.formatter import format_to_html

from trac.ticket.model import Ticket

from trac.attachment import Attachment

from genshi.builder import tag
from genshi.filters.transform import Transformer

def _get_wiki_html(env, ticketid, data, is_new_ticket):
  tk = Ticket(env, ticketid)
  attachments = []
  testers = []
  for at in Attachment.select(env, 'ticket', ticketid):
    attachments.append(at.filename)
  std_fields = [f['name'] for f in tk.fields if not f.get('custom')]
  cl = tk.get_changelog()
  for a in cl:
    if a[2] == 'comment':
      if not a[1] in testers:
        testers.append(a[1])
  if not is_new_ticket:
    text = '[One-liner summary of changes][[BR]][[BR]]' \
           '[Verbose description of the changes][[BR]][[BR]]' \
           'Closes !#%d[[BR]]' \
           'Reported by: %s[[BR]]' \
           'Patches:[[BR]] * %s[[BR]]' \
           'Tested by: %s[[BR]]' \
           'Review: [full review board URL with trailing slash]' \
           % (ticketid, tk['reporter'], '[[BR]] * '.join(attachments), ','.join(testers))
  
  return tag.fieldset(tag.legend('Commit message template'), format_to_html(env, data['context'], text))

class TicketCommentTemplateBox(Component):
  """ This component inserts a Commit message template for a ticket"""

  implements(ITemplateStreamFilter)
  
  def filter_stream(self, req, method, filename, stream, data):
    """Return a filtered Genshi event stream, or the original unfiltered
    stream if no match.

    `req` is the current request object, `method` is the Genshi render
    method (xml, xhtml or text), `filename` is the filename of the template
    to be rendered, `stream` is the event stream and `data` is the data for
    the current template.

    See the Genshi documentation for more information.
    """
    if method != 'xhtml':
      return stream
      
    if req.path_info.startswith('/ticket/') and 'TICKET_MODIFY' in req.perm:
      id = int(req.args.get('id'))
      stream = stream | Transformer('//fieldset[@id="properties"]').before(_get_wiki_html(self.env, id, data, False))
      
    return stream
