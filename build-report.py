"""Regenerate the editable project report: python docs/build-report.py.
Requires: pip install reportlab pillow
"""
from pathlib import Path
import json,io
from xml.sax.saxutils import escape
from PIL import Image as PILImage
from reportlab.pdfgen import canvas
from reportlab.platypus import SimpleDocTemplate,Paragraph,Spacer,Table,TableStyle,Image,PageBreak
from reportlab.lib.styles import getSampleStyleSheet,ParagraphStyle
from reportlab.lib import colors
from reportlab.lib.enums import TA_LEFT
from reportlab.lib.pagesizes import A4
HERE=Path(__file__).resolve().parent
D=json.loads((HERE/'REPORT-CONTENT.json').read_text())
NAVY=colors.HexColor('#101a29');GOLD=colors.HexColor('#b38c46');INK=colors.HexColor('#233045');MUTED=colors.HexColor('#617083');PAPER=colors.HexColor('#f5f6f8')
styles=getSampleStyleSheet()
styles.add(ParagraphStyle(name='BodyCustom',fontName='Helvetica',fontSize=9.5,leading=14,textColor=INK,spaceAfter=11))
styles.add(ParagraphStyle(name='SectionCustom',fontName='Helvetica-Bold',fontSize=12,leading=16,textColor=NAVY,spaceBefore=7,spaceAfter=9))
styles.add(ParagraphStyle(name='CellCustom',fontName='Helvetica',fontSize=8.3,leading=11.5,textColor=INK))
styles.add(ParagraphStyle(name='HeadCell',fontName='Helvetica-Bold',fontSize=8.2,leading=11,textColor=colors.white))
styles.add(ParagraphStyle(name='CaptionCustom',fontName='Helvetica-Oblique',fontSize=8,leading=11,textColor=MUTED,spaceBefore=6,spaceAfter=12))
styles.add(ParagraphStyle(name='PageTitleCustom',fontName='Helvetica-Bold',fontSize=23,leading=28,textColor=NAVY,spaceAfter=10))
styles.add(ParagraphStyle(name='SubCustom',fontName='Helvetica',fontSize=11,leading=16,textColor=MUTED,spaceAfter=22))
def clean(s):return str(s).replace('’',"'").replace('‘',"'").replace('–','-').replace('—','-').replace('→',' to ').replace('×','x').replace('≤','<=')
def para(s,sty='BodyCustom'):return Paragraph(escape(clean(s)).replace('\n','<br/>'),styles[sty])
def table(headers,rows,widths=None):
 t=Table([[para(x,'HeadCell') for x in headers]]+[[para(x,'CellCustom') for x in row] for row in rows],colWidths=widths or [495/len(headers)]*len(headers),repeatRows=1,hAlign='LEFT')
 t.setStyle(TableStyle([('BACKGROUND',(0,0),(-1,0),NAVY),('VALIGN',(0,0),(-1,-1),'TOP'),('LEFTPADDING',(0,0),(-1,-1),10),('RIGHTPADDING',(0,0),(-1,-1),10),('TOPPADDING',(0,0),(-1,-1),9),('BOTTOMPADDING',(0,0),(-1,-1),9),('ROWBACKGROUNDS',(0,1),(-1,-1),[PAPER,colors.white]),('LINEBELOW',(0,0),(-1,0),1,GOLD),('LINEBELOW',(0,1),(-1,-1),.3,colors.HexColor('#dde2e8'))]));return t
story=[]
# Cover is drawn on canvas; reserve one page.
story += [Spacer(1,690),PageBreak()]
for page in D['pages']:
 story.extend([para(page['title'],'PageTitleCustom'),para(page['subtitle'],'SubCustom')])
 for b in page['blocks']:
  k=b['type']
  if k=='p':story.append(para(b['text']))
  elif k=='h':story.append(para(b['text'],'SectionCustom'))
  elif k=='table':story.extend([table(b['headers'],b['rows'],b.get('widths')),Spacer(1,12)])
  elif k=='image':
   im=PILImage.open(HERE/b['file']).convert('RGB');im=im.crop((0,0,im.width,min(im.height,b.get('crop_height',im.height))));buf=io.BytesIO();im.save(buf,format='PNG');buf.seek(0);ratio=min(495/im.width,b.get('max_height',320)/im.height);story.extend([Image(buf,width=im.width*ratio,height=im.height*ratio,hAlign='LEFT'),para(b['caption'],'CaptionCustom')])
  elif k=='contributions':story.extend([table(['Member / index','Actual contribution'],[[x['name']+'\n'+x['index'],x['contribution']] for x in D['members']],[185,310]),Spacer(1,14)])
  elif k=='github':story.append(para('GitHub URL: '+D['github']))
 story.append(PageBreak())
story.pop()
def frame(c,doc):
 w,h=A4
 if doc.page==1:
  c.setFillColor(NAVY);c.rect(0,0,w,h,fill=1,stroke=0);c.setFillColor(GOLD);c.rect(48,h-93,58,4,fill=1,stroke=0)
  c.setFont('Helvetica-Bold',10);c.drawString(48,h-126,'FINAL PROJECT REPORT / 2026')
  c.setFillColor(colors.white);c.setFont('Times-Roman',44);c.drawString(48,h-206,'Falcon Perfumes')
  c.setFillColor(colors.HexColor('#d7dde5'));c.setFont('Helvetica',17);c.drawString(48,h-242,D['subtitle'])
  y=h-302
  for label,value in [('THEME',D['theme']),('COURSE',D['course']),('REPORT DATE',D['date'])]:
   c.setFillColor(GOLD);c.setFont('Helvetica-Bold',8);c.drawString(48,y,label);c.setFillColor(colors.white);c.setFont('Helvetica',11);c.drawString(48,y-20,clean(value));y-=64
  c.setStrokeColor(colors.HexColor('#3b4554'));c.line(48,y+10,w-48,y+10);y-=18
  c.setFont('Helvetica-Bold',9);c.setFillColor(GOLD);c.drawString(48,y,'GROUP MEMBERS');c.drawString(325,y,'INDEX NUMBERS');y-=28
  for member in D['members']:
   c.setFont('Helvetica',10);c.setFillColor(colors.white);c.drawString(48,y,clean(member['name']));c.drawString(325,y,clean(member['index']));y-=28
  c.setFillColor(colors.HexColor('#aab7c6'));c.setFont('Helvetica',9);c.drawString(48,95,'Complete group details, contribution records and GitHub URL before LMS submission.')
  c.setFillColor(GOLD);c.setFont('Helvetica-Bold',8);c.drawString(48,55,'SOURCE CODE  /  DATABASE  /  IMPLEMENTATION  /  SETUP')
 else:
  c.setFillColor(NAVY);c.rect(0,h-39,w,39,fill=1,stroke=0);c.setFillColor(colors.white);c.setFont('Helvetica-Bold',8);c.drawString(50,h-25,'FALCON PERFUMES');c.setFont('Helvetica',8);c.drawRightString(w-50,h-25,'PHP & MYSQL INTEGRATION')
  c.setStrokeColor(colors.HexColor('#d8dde3'));c.line(50,43,w-50,43);c.setFillColor(MUTED);c.setFont('Helvetica',8);c.drawString(50,29,'Final Project Report | '+D['date']);c.drawRightString(w-50,29,str(doc.page))
out=HERE/'Falcon-Perfumes-Project-Report.pdf'
doc=SimpleDocTemplate(str(out),pagesize=A4,rightMargin=50,leftMargin=50,topMargin=64,bottomMargin=59,title='Falcon Perfumes - Phase 3 Project Report',author='Project group - complete details before submission')
doc.build(story,onFirstPage=frame,onLaterPages=frame)
print(out)
