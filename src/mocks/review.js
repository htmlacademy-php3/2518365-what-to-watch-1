const reviews = [
  {
    'id': 1,
    'user': {
      'id': 4,
      'name': 'Kate Muir',
    },
    'rating': 8.9,
    'comment': 'Discerning travellers and Wes Anderson fans will luxuriate in the glorious Mittel-European kitsch of one of the directors funniest and most exquisitely designed movies in years.',
    'date': '2019-05-08T14:13:56.569Z',
  },
  {
    'id': 2,
    'user': {
      'id': 8,
      'name': 'Bill Goodykoontz',
    },
    'rating': 8.0,
    'comment': 'Andersons films are too precious for some, but for those of us willing to lose ourselves in them, theyre a delight. "The Grand Budapest Hotel" is no different, except that he has added a hint of gravitas to the mix, improving the recipe.',
    'date': '2015-11-18T20:13:56.569Z',
  },
  {
    'id': 3,
    'user': {
      'id': 13,
      'name': 'Amanda Greever',
    },
    'rating': 6.0,
    'comment': 'I didnt find it amusing, and while I can appreciate the creativity, its an hour and 40 minutes I wish I could take back.',
    'date': '2015-11-18T20:13:56.569Z',
  },
  {
    'id': 3,
    'user': {
      'id': 3,
      'name': 'Matthew Lickona',
    },
    'rating': 7.2,
    'comment': 'The mannered, madcap proceedings are often delightful, occasionally silly, and here and there, gruesome and/or heartbreaking.',
    'date': '2016-12-20T20:13:56.569Z',
  },
  {
    'id': 4,
    'user': {
      'id': 9,
      'name': 'Matthew Lickona',
    },
    'rating': 7.6,
    'comment': 'It is certainly a magical and childlike way of storytelling, even if the content is a little more adult.',
    'date': '2016-12-20T20:13:56.569Z',
  },
  {
    'id': 4,
    'user': {
      'id': 11,
      'name': 'Paula Fleri-Soler',
    },
    'rating': 7.0,
    'comment': 'It is certainly a magical and childlike way of storytelling, even if the content is a little more adult.',
    'date': '2016-12-20T20:13:56.569Z',
  },
];


const adaptToClient = (review) => (
  {
    id: review.id,
    userId: review.user.id,
    userName: review.user.name,
    rating: review.rating,
    comment: review.comment,
    date: review.date,
  }
);

export default reviews.map((review) => adaptToClient(review));
